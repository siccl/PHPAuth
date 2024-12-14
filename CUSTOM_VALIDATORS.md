# What is it?

All custom validators takes a closure function as a single argument.

This is a function that accepts a string (password or email) and returns boolean (true or false).
Closure can accept additional parameters with `use ()` expression.

This function must return:
- true if email or password is valid (passed validation)
- false othervise

# Password custom validators

Initialized with `setPasswordValidator()` method.

Called at:
- register
- @todo

## bjeavons/zxcvbn-php

`composer require bjeavons/zxcvbn-php`

```php
use ZxcvbnPhp\Zxcvbn;

$config = $config->setPasswordValidator(static function($password) {
    // explicitly declare minimum password strength, see below
    $password_min_score = 3;
    return (bool)((new Zxcvbn())->passwordStrength($password)['score'] >= $password_min_score));
});
```
NB: it is better not to take the minimum password strength from the config, but to specify it explicitly - because this parameter may be different for different handlers.

used as:

```php
private function isPasswordStrong(string $password):bool
{
    if (is_callable($this->passwordValidator)) {
        return ($this->passwordValidator)($password);
    }

    return true;
}
```

Custom validator MUST return **TRUE** if the password is strong enough and **FALSE** otherwise.

Without a defined validator, the password is always considered strong enough.

Developer can use other validator implementations, such as:

- https://packagist.org/packages/rollerworks/password-strength-validator
- https://packagist.org/packages/valorin/pwned-validator
- https://packagist.org/packages/schuppo/password-strength
- https://packagist.org/packages/jbafford/password-strength-bundle
- https://packagist.org/packages/garybell/password-validator (Password validation determined by password entropy)
- https://packagist.org/packages/rollerworks/password-common-list
- and so on...

# E-Mail custom validators

Initialized with `setEmailValidator()` method.

EMail validator MUST return **true** if email is valid (for example, not in banlist), **false** otherwise.

Used as:
```php

if (is_callable($this->emailValidator) && !call_user_func_array($this->emailValidator, [ $email ])) {
    $this->addAttempt();
    $state['message'] = $this->__lang('email.address_in_banlist');

    return $state;
}
```

## phpauth/phpauth.email-validator (recommended)

```php

$config = $config->setEMailValidator(static function ($email) {
    return \PHPAuth\EMailValidator::isValid($email);
});


```
## mattketmo/email-checker (PHP 7.1+)

Throwaway email detection library.

`composer require mattketmo/email-checker`

```php
use EmailChecker\EmailChecker;

$config = $config->setEMailValidator(static function ($email) {
    return (new EmailChecker())->isValid($email);
});
```

## fgribreau/mailchecker (PHP 7.3+)

Temporary (disposable/throwaway) email detection library. Covers 1987 fake email providers.
NB: Package does not use namespaces.

`composer require fgribreau/mailchecker`

```php
$config = $config->setEMailValidator(static function ($email) {
    return MailChecker::isValid($email);
});
```

# Captcha validators

## Google reCaptcha (google/recaptcha)

https://packagist.org/packages/google/recaptcha

```php
$captcha_response = $_POST['g-recaptcha-response'];

$config = $config->setCaptchaValidator(static function($captcha_response) use ($reCaptcha_config) {
    if (empty($reCaptcha_config)) {
        return true;
    }

    if ($reCaptcha_config['recaptcha_enabled'] == false) {
        return true;
    }

    if (empty($reCaptcha_config['recaptcha_secret_key'])) {
        throw new RuntimeException('No secret provided');
    }

    if (!is_string($reCaptcha_config['recaptcha_secret_key'])) {
        throw new RuntimeException('The provided secret must be a string');
    }

    $recaptcha = new ReCaptcha($reCaptcha_config['recaptcha_secret_key']);
    $checkout = $recaptcha->verify($captcha_response, \PHPAuth\Helpers::getIp());

    if (!$checkout->isSuccess()) {
        return false;
    }

    return true;
}, $reCaptcha_config);
```



