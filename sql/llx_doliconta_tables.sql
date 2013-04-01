-- ============================================================================
-- Copyright (C) 2013 Manuel Munoz <mmunoz@iconhn.com>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ===========================================================================

CREATE TABLE llx_iconta_accountingdebcred (
  fk_transaction int(11) NOT NULL,
  fk_accountid int(11) NOT NULL,
  amount double NOT NULL,
  direction varchar(1) NOT NULL,
  rowid int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (rowid),
  KEY fk_transaction (fk_transaction),
  KEY fk_accountid (fk_accountid)
) ENGINE=InnoDB ;


CREATE TABLE IF NOT EXISTS llx_iconta_accountingtransaction (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  entity int(11) NOT NULL DEFAULT '1',
  number varchar(30) NOT NULL,
  label varchar(128) NOT NULL,
  datec date NOT NULL,
  fk_author varchar(20) NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fk_source int(11) NOT NULL,
  sourcetype varchar(16) NOT NULL,
  url varchar(255) DEFAULT NULL,
  status int(11) NOT NULL DEFAULT '0',
  fk_fiscal_year int(11) NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY unique_number (number),
  KEY fk_fiscal_year (fk_fiscal_year),
  KEY fk_fiscal_year_2 (fk_fiscal_year)
) ENGINE=InnoDB  ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_bankmatch
--

CREATE TABLE IF NOT EXISTS llx_iconta_bankmatch (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  fk_id_bank_account int(11) NOT NULL,
  fk_id_account int(11) NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user int(11) NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY fk_id_bank_account (fk_id_bank_account,fk_id_account)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_banktransaction
--

CREATE TABLE IF NOT EXISTS llx_iconta_banktransaction (
  id_bank int(11) NOT NULL,
  id_accountingtrans int(11) NOT NULL,
  PRIMARY KEY (id_bank,id_accountingtrans),
  KEY id_accountingtrans (id_accountingtrans)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_chart_class
--

CREATE TABLE IF NOT EXISTS llx_iconta_chart_class (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  class_name varchar(60) NOT NULL DEFAULT '',
  ctype varchar(2) NOT NULL DEFAULT 'ND',
  inactive tinyint(1) NOT NULL DEFAULT '0',
  ordernum int(11) DEFAULT '0',
  PRIMARY KEY (rowid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_chart_master
--

CREATE TABLE IF NOT EXISTS llx_iconta_chart_master (
  account_code varchar(15) NOT NULL DEFAULT '',
  account_code2 varchar(15) NOT NULL DEFAULT '',
  account_name varchar(60) NOT NULL DEFAULT '',
  account_type int(11) NOT NULL DEFAULT '0',
  inactive tinyint(1) NOT NULL DEFAULT '0',
  rowid int(11) NOT NULL AUTO_INCREMENT,
  description text NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY uniqueaccountcode1 (account_code,account_code2),
  UNIQUE KEY account_name (account_name)
) ENGINE=InnoDB  ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_chart_types
--

CREATE TABLE IF NOT EXISTS llx_iconta_chart_types (
  rowid int(10) NOT NULL AUTO_INCREMENT,
  name varchar(60) NOT NULL DEFAULT '',
  class_id int(11) NOT NULL DEFAULT '0',
  parent int(11) NOT NULL DEFAULT '-1',
  inactive tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (rowid),
  KEY name (name),
  KEY class_id (class_id)
) ENGINE=InnoDB ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_entry_types
--

CREATE TABLE IF NOT EXISTS llx_iconta_entry_types (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  label varchar(15) NOT NULL,
  name varchar(100) NOT NULL,
  description varchar(255) NOT NULL,
  base_type int(2) NOT NULL,
  numbering int(2) NOT NULL,
  prefix varchar(10) NOT NULL,
  suffix varchar(10) NOT NULL,
  zero_padding int(2) NOT NULL,
  bank_cash_ledger_restriction int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (rowid)
) ENGINE=InnoDB   ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_fiscal_year
--

CREATE TABLE IF NOT EXISTS llx_iconta_fiscal_year (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  begin date DEFAULT '0000-00-00',
  end date DEFAULT '0000-00-00',
  closed tinyint(4) NOT NULL DEFAULT '0',
  label varchar(50) NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY begin (begin),
  UNIQUE KEY end (end)
) ENGINE=InnoDB ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_gl_trans
--

CREATE TABLE IF NOT EXISTS llx_iconta_gl_trans (
  counter int(11) NOT NULL AUTO_INCREMENT,
  type smallint(6) NOT NULL DEFAULT '0',
  type_no bigint(16) NOT NULL DEFAULT '1',
  tran_date date NOT NULL DEFAULT '0000-00-00',
  account varchar(15) NOT NULL DEFAULT '',
  memo_ tinytext NOT NULL,
  amount double NOT NULL DEFAULT '0',
  dimension_id int(11) NOT NULL DEFAULT '0',
  dimension2_id int(11) NOT NULL DEFAULT '0',
  person_type_id int(11) DEFAULT NULL,
  person_id tinyblob,
  PRIMARY KEY (counter),
  KEY Type_and_Number (type,type_no),
  KEY dimension_id (dimension_id),
  KEY dimension2_id (dimension2_id),
  KEY tran_date (tran_date),
  KEY account_and_tran_date (account,tran_date)
) ENGINE=InnoDB  ;

-- --------------------------------------------------------

--
-- Table structure for table llx_iconta_loan_payment_transaction
--

CREATE TABLE IF NOT EXISTS llx_iconta_loan_payment_transaction (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  fk_loan_payment int(11) NOT NULL,
  fk_accounting_transaction int(11) NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY pkloanpayment (fk_loan_payment,fk_accounting_transaction),
  KEY fk_accounting_transaction (fk_accounting_transaction),
  KEY fk_loan_payment (fk_loan_payment)
) ENGINE=InnoDB ;
