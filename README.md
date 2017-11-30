# namesilo-php-sdk

Simple PHP SDK for registering domain names using Namesilo.com.

### Dependencies

* php-xml package.

Install in Debian:

```
sudo aptitude install php7.0-xml
```

### Quick start

Create an instance of the Namesilo class.

> To receive a sandbox API key for testing, please contact namesilo.

```php
#### create a Namesilo object with production key
$ns = new Namesilo('your api key');

#### create a Namesilo object with sandox key
$ns = new Namesilo('your api key',true);

#### turn Debugging on (if you want to fix an issue with this library)
$ns = new Namesilo('your api key',true,true);

#### Simple usage
try{
    $ns->is_domain_available('example.com');
}catch(Exception $e){
    #### print error message
    echo $e.getMessage();
}
```

### Debugging

To enable debugging edit namesilo.php provide a third argument to Namesilo() and set it to `tre`.

```php
$ns = new Namesilo('your api key',true,true);
```

Once enabled, all functions (methods)  `print_r()`  their request and return value.

> never turn debugging on in  production.

### Usage example

Retrieve the lock status for exmaple.com

```php
try{
    $lock_status = $ns->lock_status('example.com');
}catch(Exception $e){
    echo $e->getMessage();
}
if($lock_status){
    echo 'domain is lock';
}else{
    echo 'domain is unlock';
}
```

### List of methods

> NOTE: make sure that you capture possible errors with a `try {} catch {}` as shown in the above examples.

- [create_contact()](#create_contact):  create a new contact id. Used for new domain registration.
- [delete_contact()](#delete_contact): useful for deleting a contact if registration fails
- [register_domain_by_contact_id()](#register_domain_by_contact_id)
- [register_domain()](#register_domain): create a new contact id and register domain at once (not recommended)
- [update_nameservers()](#update_nameservers)
- [update_contact_by_domain()](#update_contact_by_domain)
- [add_dns_record()](#add_dns_record)
- [get_dns_records()](#get_dns_records): returns an array of dns records
- [delete_dns_record()](#delete_dns_record)
- [is_domain_available()](#is_domain_available)
- [send_auth_code()](#send_auth_code): Have the EPP transfer code for the domain emailed to the administrative contact.
- [get_contact_by_id()](#get_contact_by_id)
- [list_domains()](#list_domains)
- [get_nameservers()](#get_nameservers)
- [privacy_status()](#privacy_status)
- [add_privacy()](#add_privacy)
- [remove_privacy()](#remove_privacy)
- [get_domain_info()](#get_domain_info)
- [get_account_balance()](#get_account_balance)


<a name="create_contact"/>

### create_contact()


Use this function to create a new contact-id and then use the contact-id retuned by this function to associate it with a new domain registration.

> returns created contact id on success and false on failure.

```php
$contact_id = $ns->create_contact(
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
```



<a name="register_domain_by_contact_id"/>

### register_domain_by_contact_id()

> returns true on success and false on failure.

```php
$result = $ns->register_domain_by_contact_id($domain,$contact_id,$years=1);
```

<blockquote>
    This function will automatically attemp to delete the contact if registration fails.
</blockquote>

<blockquote>
    Default profile and profiles that are associated with any active domains cannot be deleted.
</blockquote>


<a name="register_domain"/>

### register_domain()

Create a new contact-id and register a domain for it at once. This method is not recommended.

> returns true on success and false on failure.

```php
$ns->register_domain(
      $domain, // example.com
      $fn, // first name
      $ln, // last name
      $ad, // address
      $cy, // city
      $st, // state
      $zp, // zip
      $ct, // country
      $em, // email
      $ph,  // phone
      $years = 1
);
```

<a name="update_contact_by_domain"/>

### update_contact_by_domain()

> returns true on success and false on failure.

```php
$contact_id = $ns->update_contact_by_domain(
      $domain, // example.com
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
```

<a name="update_nameservers"/>

### update_nameservers()

Change the NameServers associated with one of your domains.

> returns true on success and false on success and false on failure.

```php
$result = $ns->update_nameservers('example.com','ns1.namesilo.com','ns2.namesilo.com');
```


<a name="delete_contact"/>

### delete_contact()

> returns true on success and false on failure.

```php
$ns->delete_contact($contact_id);
```

> Please remember that the only contact profiles that can be deleted are those that are not the account default and are not associated with any active domains or order profiles.


<a name="add_dns_record"/>

### add_dns_record()

> returns true on success and false on failure.

```php
$ns->add_dns_record($domain,$type,$host,$value,$distance='',$ttl='');
```

<blockquote>
    $type possible values are "A", "AAAA", "CNAME", "MX" and "TXT"
</blockquote>

<blockquote>
    $host  The hostname for the new record (there is no need to include the ".DOMAIN")
</blockquote>

<blockquote>
$value
    <ul>
        <li>A - The IPV4 Address</li>
        <li>AAAA - The IPV6 Address</li>
        <li>CNAME - The Target Hostname</li>
        <li>MX - The Target Hostname</li>
        <li>TXT - The Text</li>
    </ul>
</blockquote>

<blockquote>
    $distance: Only used for MX (default is 10 if not provided)
</blockquote>

<blockquote>
    $ttl: The TTL for the new record (default is 7207 if not provided)
</blockquote>


<a name="get_dns_records"/>

### get_dns_records()

> returns an array of records on success and false on failure.

```php
$ns->get_dns_records($domain);
```

Sample `print_r()` of successfull return value:

> Again, make sure you capture exceptions.

```
Array
(
    [0] => Array
        (
            [record_id] => a8c4251d3c3d114d70d48dcaf0288257
            [type] => A
            [host] => sunsed-test15.com
            [value] => 173.255.255.106
            [ttl] => 7200
            [distance] => 0
        )

)
```


<a name="delete_dns_record"/>

### delete_dns_record()

> returns true on success and false on failure.

```php
$ns->delete_dns_record($domain,$record_id);
```


<a name="is_domain_available"/>

### is_domain_available()

> possible return values: 'available', 'invalid', 'unavailable' and false.

```php
try{
    $result = $ns->is_domain_available($domain);
    if($result == 'available'){
        echo 'domain is available';
    }elseif($result == 'invalid'){
        echo 'pleas check your domain';
    }elseif($result == 'unavailable'){
        echo 'domain is not available';
    }else{
        echo 'failed';
    }
}catch(Exception $e){
    echo $e->getMessage();
}
```


<a name="send_auth_code"/>

### send_auth_code()

> returns true on success and false on failure.

```php
ns->send_auth_code($domain);
```


<a name="get_contact_by_id"/>

### get_contact_by_id()

> returns contact info on success and false on failure.

```php
$ns->get_contact_by_id($contact_id);
```

<a name="list_domains"/>

### list_domains()

> returns an array of domains on success and false on failure.

```php
try{
    $result = $ns->list_domains();
    if($result){
        echo 'success';
    }else{
        echo 'failed';
    }
}catch(Exception $e){
    echo $e.getMessage();
}
```

Sample return value:

```
Array
(
    [0] => sunsed-test12.com
    [1] => sunsed-test13.com
    [2] => sunsed-test15.com
)
```

<a name="get_nameservers"/>

### get_nameservers()

> returns an array of nameservers on success and false on failure.

Sample return value:

```
Array
(
    [0] => NS1.NAMESILO.COM
    [1] => NS2.NAMESILO.COM
)
```

> Note: Namesilo returns the namesevrers in capital letters.

<a name="privacy_status"/>

### privacy_status()

Retrieves the privacy status.

```php
$result = $ns->privacy_status($domain);
if($result)
    echo 'Privacy is enabled';
else
    echo 'Privacy is disabled';
```

<a name="add_privacy"/>

### add_privacy()

```php
$result = $ns->add_privacy($domain);
```

<a name="remove_privacy"/>

### remove_privacy()

```php
$result = $ns->remove_privacy($domain);
```

<a name="get_domain_info"/>

### get_domain_info()

> returns domain-info-array on success and false on failure.

```php
$ns->get_domain_info($domain);
```

Sample return value:

```
Array
(
    [code] => 300
    [detail] => success
    [created] => 2015-02-06
    [expires] => 2016-02-06
    [status] => Active
    [locked] => Yes
    [private] => Yes
    [auto_renew] => No
    [traffic_type] => Custom DNS
    [forward_url] => N/A
    [forward_type] => N/A
    [nameservers] => Array
        (
            [nameserver] => Array
                (
                    [0] => NS1.NAMESILO.COM
                    [1] => NS2.NAMESILO.COM
                )

        )

    [contact_ids] => Array
        (
            [registrant] => 903
            [administrative] => 903
            [technical] => 903
            [billing] => 903
        )

)
```

<a name="get_account_balance"/>

### get_account_balance()

> returns account balance on success and false on failure.

```php
$result = $ns->get_account_balance();
```
