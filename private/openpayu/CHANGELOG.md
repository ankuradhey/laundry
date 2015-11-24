## 2.1.0
* Endpoint https://secure.payu.com/api/v2_1/orders
* Simplification of request structure by eliminating nesting
* Status code 200 as only response after receiving notification
* Parameters starting with lowercase letters
* CurrencyCode field removed from refund create request
* CompleteUrl replaced with continueUrl
* Value of optional field extOrderId must be unique within one point of sale (POS)

## 2.0.8
* More data in OrderCreate.php example: addition of invoice and delivery optional sections

## 2.0.7

* README.md update
* CHANGELOG.md update
* Cleaned and fixed links in OrderCreate.php example
* ContinueUrl.php deleted
* OpenPayU_Util::statusDesc($response) function update

## 2.0.6

* GeneratedOrderForm.php removal

## 2.0.5

* Fixed bugs in examples.

## 2.0.4

* Fixed bugs

## 2.0.3

* Added tracking of version

## 2.0.2

* Fixed some bugs
* Updated README.md

## 2.0.1

* Fixed some bugs
* Removed support for XML messages
* Removed unsupported examples
* Fixed problem with uppercase keys in order array

## 2.0

* Removed support for OpenPayU 1.0
* Added support for OpenPayuU REST API
* Fixed bugs
* Added unit tests

## 1.9.3

* Added order.cancel and order.statusUpdate functions
* Added hostedOrderForm function
* Unification according to ruby_sdk. Improve comments.

## 1.9.2

* Added protection against full path disclosure

## 0.1.9.1

* Added messages of results
* Changed method invokes

## 0.1.9
* Contains bug fixes 0.1.8 version
* Added PHPDoc and formatted code