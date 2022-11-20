# Changelog

## 3.0.0

- Added `default` option to saved card tokens.
- Added cardholder name, expiration date, and brand to saved card tokens.
- **BREAKING!** Active cards are automatically filtered in PayzeCardToken model via global scope. Scope `active` model will now filter **non-expired** cards, instead of activated ones.
  
    Before: `$query->where('active', true)`
  
    After: `$query->where('expiration_date', '>=', Carbon::now())`

    Please refer to [this section](README.md#card-token-model) to read more details.

## v2.0.0

- Bumped PHP to version 7.4, Added property types ([7ec95e2](https://github.com/payzeio/laravel-payze/commit/7ec95e29b5a7e220cdde68384dfaabf955f9c134))
- Use `Relation::morphMap()` aliases for transactions and card tokens tables ([133dcab](https://github.com/payzeio/laravel-payze/commit/133dcab7e7526c1c99678e22eafa8f271caf744a))
- Fixed split method ([0a7a719](https://github.com/payzeio/laravel-payze/commit/0a7a719cce6be862f73055107dc97e16cac02e64))
- Minor bug fixes

## v1.9.0

- Added Transaction ID column to PayzeLog model ([ee30e45](https://github.com/payzeio/laravel-payze/commit/ee30e45ce52bd20a6bfb4e70eee300eb8787a30d))

## v1.8.5

- Added support of UZB language ([d6dd3a7](https://github.com/payzeio/laravel-payze/commit/d6dd3a7ba2a909e319fd77dc92f355e5497829a9))

## v1.8.3

- Added support of UZS currency ([ee76cc5](https://github.com/payzeio/laravel-payze/commit/ee76cc5f8b26683f639586ed59f772cee6f39bcc))
