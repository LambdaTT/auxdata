# lambdatt-php/auxdata

A SplitPHP Framework plugin which provide useful auxiliar data.

---

## Installation

Install via Composer:

```bash
composer require lambdatt-php/auxdata
```

Run the Migrations:
```bash
php console migrations:apply --module=auxdata
```

Config which aux entities you want to use:
file "config.json"
```json
{
    "entities": [
        "marital_status",
        "kinship"
    ]
}
```

**PS: this can only be installed on a SplitPHP Framework project. For more information refer to: https://github.com/splitphp/core**

## Usage: