Fix CI
======

A small and simple CLI utility, that allows to run the tasks from common CI tools.

Installation
------------

```bash
composer global require becklyn/cert-key-matcher
```


Usage
-----

Call the CLI tool from the project directory:

```bash
fix-ci
```

By default the tool runs checks (like tests) and all fixers.

If you only want to run the fixers and skip the tests, pass the `--only-fix` option.
