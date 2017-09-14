# MODX Version Broadcast

A MODX Revolution extra that can expose the current MODX version via a REST (secret) REST endpoint.

The endpoint is securet by a token that has to be generated with a given key.

## Requirements

This extra makes uses of the [`password_hash`](http://php.net/password_hash) and [`password_verify`](http://php.net/password_verify) functions in PHP. These were introduces in PHP 5.5. This is therefore the minimal requirement.



## Generate key

You can use the following method to generate the key before sending the request to the destination:

```
function generateToken($secret, $salt) {
    return password_hash($secret . ',,' . $salt, \PASSWORD_BCRYPT);
}
```
