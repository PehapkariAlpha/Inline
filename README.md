XcoreCMS/InlineEditing
======================

[![Build Status](https://travis-ci.org/pehapkari-alpha/inline-editable.svg?branch=master)](https://travis-ci.org/pehapkari-alpha/inline-editable)
[![Coverage Status](https://coveralls.io/repos/github/pehapkari-alpha/inline-editable/badge.svg?branch=master)](https://coveralls.io/github/pehapkari-alpha/inline-editable?branch=master)

Inline Editing = Content editable...


Requirements
------------

XcoreCMS/InlineEditing requires PHP 7.1 or higher


Installation
------------

The best way to install XcoreCMS/InlineEditing is using [Composer](http://getcomposer.org/):

```bash
    composer require xcore/inline-editing
```

#### Create database table example

```bash
    php bin/install.php dns="mysql:host=127.0.0.1;dbname=test" username=root password=pass tableName=table

    # parameters:
    #   dns - required
    #   username - required
    #   password - optional
    #   tableName - optional (default `inline_content`)
```