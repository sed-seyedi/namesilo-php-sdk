# namesilo-php-sdk
A standalone, fully documented, bugless, fast and super easy to use PHP SDK to register domain names.

<a name="top"></a>
#### Quick start

Set `$ns_key` to your api key provided by namesilo and then include namesilo.php in your application.

#### Setting API key
In namesilo.php file set your api key:
```php
$ns_key = 'your api key goes here';
```
> To receive a sandbox API key for testing, please contact namesilo.

#### Production vs Development
For development edit namesilo.php and set
```php
$ns_url = 'http://sandbox.namesilo.com/api/';
```
For production set
```php
$ns_url = 'https://www.namesilo.com/api/'; 
```

#### Debugging 
To enable debugging edit namesilo.php and set $ns_debug to true.
```php
$ns_debug = true;
```
Once enabled, all `ns_*`  functions   `print_r()`  their request and return value.

> never turn debugging on in  production.

#### Usage example
Retrieve the lock status for exmaple.com

```php
$lock_status = ns_lock_status('example.com');

if($lock_status)
    echo 'domain is lock';
else
    echo 'domain is unlock';
```

#### Usage example 2
Locking example.com:

```php
if(ns_domain_lock('example.com')){
   echo 'Successfully locked the domain.';
}else{
   echo 'The reason it failed: ' . $ns_error;
}
```
<blockquote>
 ns_domain_lock() retruns true on success and false on failure.
</blockquote>
<blockquote>
$ns_error is a global variable set by ns_domain_lock()
</blockquote>
<blockquote>
	if you call ns_domain_lock() inside another function make sure to define $ns_error as a global variable <code>global $ns_error</code>
</blockquote>

#### List of functions

- [ns_create_contact()](#ns_create_contact):  create a new contact id for later use.
- [ns_update_nameservers()](#ns_update_nameservers);
- ns_update_contact_by_domain()
- [got_to_top()](#top)


<a name="ns_create_contact"/>
#### ns_create_contact()
Use this function to create a contact info and then use the contact id retuned by this function to associate it with a new domain registration.
> returns created contact id on success and false on failure.
```php
$contact_id = ns_create_contact(
      $fn, // first name
      $ln, // last name
      $ad, // address
      $cy, // city
      $st, // state
      $zp, // zip
      $ct, // country
      $em, // email
      $ph  // phone
);
if($contact_id){
    echo 'new contact id: ' . $contact_id;
}else{
    echo 'could not create contact id because: ' . $ns_error;
}
```

<a name="ns_create_contact"/>
#### ns_update_nameservers()


