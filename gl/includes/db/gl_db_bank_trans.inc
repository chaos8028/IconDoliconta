<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
//----------------------------------------------------------------------------------

// add a bank transaction
// $amount is in $currency
// $date_ is display date (non-sql)

function add_bank_trans($type, $trans_no, $bank_act, $ref, $date_,
	$amount, $person_type_id, $person_id, $currency="", $err_msg="", $rate=0)
{
	$sqlDate = date2sql($date_);

	// convert $amount to the bank's currency
	if ($currency != "")
	{
    	$bank_account_currency = get_bank_account_currency($bank_act);
    	if ($rate == 0)
    		$to_bank_currency = get_exchange_rate_from_to($currency, $bank_account_currency, $date_);
    	else
    		$to_bank_currency = 1 / $rate;
    	$amount_bank = ($amount / $to_bank_currency);
	}
	else
		$amount_bank = $amount;
	$amount_bank = round2($amount_bank, user_price_dec());	

	// Also store the rate to the home
	//$BankToHomeCurrencyRate = get_exchange_rate_to_home_currency($bank_account_currency, $date_);

	$sql = "INSERT INTO ".TB_PREF."bank_trans (type, trans_no, bank_act, ref,
		trans_date, amount, person_type_id, person_id) ";

	$sql .= "VALUES ($type, $trans_no, '$bank_act', ".db_escape($ref).", '$sqlDate',
		".db_escape($amount_bank).", ".db_escape($person_type_id)
		.", ". db_escape($person_id).")";

	if ($err_msg == "")
		$err_msg = "The bank transaction could not be inserted";

	db_query($sql, $err_msg);
}

//----------------------------------------------------------------------------------------

function exists_bank_trans($type, $type_no)
{
	$sql = "SELECT trans_no FROM ".TB_PREF."bank_trans WHERE type=".db_escape($type)
		." AND trans_no=".db_escape($type_no);
	$result = db_query($sql, "Cannot retreive a bank transaction");

    return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------

function get_bank_trans($type, $trans_no=null, $person_type_id=null, $person_id=null)
{
	$sql = "SELECT *, bank_account_name, account_code, bank_curr_code
		FROM ".TB_PREF."bank_trans, ".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_accounts.id=".TB_PREF."bank_trans.bank_act ";
	if ($type != null)
		$sql .= " AND type=".db_escape($type);
	if ($trans_no != null)
		$sql .= " AND ".TB_PREF."bank_trans.trans_no = ".db_escape($trans_no);
	if ($person_type_id != null)
		$sql .= " AND ".TB_PREF."bank_trans.person_type_id = ".db_escape($person_type_id);
	if ($person_id != null)
		$sql .= " AND ".TB_PREF."bank_trans.person_id = ".db_escape($person_id);
	$sql .= " ORDER BY trans_date, ".TB_PREF."bank_trans.id";

	return db_query($sql, "query for bank transaction");
}

//----------------------------------------------------------------------------------------

function get_bank_trans_for_bank_account($bank_account, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT t.* FROM "
		.TB_PREF."bank_trans t LEFT JOIN ".TB_PREF."voided v ON t.type=v.type AND t.trans_no=v.id
		WHERE t.bank_act = ".db_escape($bank_account) . "
		AND ISNULL(v.date_)
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		ORDER BY trans_date, t.id";

	return db_query($sql,"The transactions for '" . $bank_account . "' could not be retrieved");
}

//----------------------------------------------------------------------------------------

function get_balance_before_for_bank_account($bank_account, $from)
{
	$from = date2sql($from);
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act="
		.db_escape($bank_account) . "
		AND trans_date < '$from'";
	$before_qty = db_query($sql, "The starting balance on hand could not be calculated");
	$bfw_row = db_fetch_row($before_qty);
	return $bfw_row[0];
}
//----------------------------------------------------------------------------------------

function get_gl_trans_value($account, $type, $trans_no)
{
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans WHERE account="
	.db_escape($account)." AND type=".db_escape($type)
	." AND type_no=".db_escape($trans_no);

	$result = db_query($sql, "query for gl trans value");

	$row = db_fetch_row($result);
	return $row[0];
}

//----------------------------------------------------------------------------------------

function void_bank_trans($type, $type_no, $nested=false)
{

	if (!$nested)
		begin_transaction();

	$sql = "UPDATE ".TB_PREF."bank_trans SET amount=0
		WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	$result = db_query($sql, "could not void bank transactions for type=$type and trans_no=$type_no");

	void_gl_trans($type, $type_no, true);

	// in case it's a customer trans - probably better to check first
	void_cust_allocations($type, $type_no);
	void_customer_trans($type, $type_no);

	// in case it's a supplier trans - probably better to check first
	void_supp_allocations($type, $type_no);
	void_supp_trans($type, $type_no);

	void_trans_tax_details($type, $type_no);

	if (!$nested)
		commit_transaction();
}

/**
*	Check account history to find transaction which would exceed authorized overdraft for given account.
*	Returns null or transaction in conflict. Running balance is checked on daily basis only, to enable ID change after edition.
*	$delta_amount - tested change in bank balance at $date.
**/
function check_bank_account_history($delta_amount, $bank_account, $date, $user=null)
{
	if ($delta_amount >= 0)
		 return null;	// amount increese is always safe

	$balance = get_bank_account_limit($bank_account, $date, $user);

	if (!isset($balance))
		return null;	// unlimited account

	$date = date2sql($date);
	$balance+= $delta_amount;

	$sql = "SELECT sum(amount) as amount FROM ".TB_PREF."bank_trans WHERE bank_act=".db_escape($bank_account)
		." AND trans_date >= '$date' GROUP BY trans_date ORDER BY trans_date ASC";

	$history = db_query($sql, "cannot retrieve cash account history");

	while ($trans = db_fetch($history)) {
		$balance += $trans['amount'];
		if ($balance < 0)
			return $trans;
	}

	return null;
}

/**
*	Check bank transfer, deposit or customer deposit before voiding.
**/
function check_void_bank_trans($type, $type_no)
{
	$moves = get_bank_trans($type, $type_no);
	while ($trans = db_fetch($moves)) {
		if ($trans['amount'] > 0) { // skip transfer input part
			return check_bank_account_history(-$trans['amount'], $trans['bank_act'], sql2date($trans['trans_date'])) == null;
		}
	}
	return true;
}

?>
