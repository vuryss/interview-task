# Optimisation interview task

To execute:

`composer install`

`php app.php app:calculate-commission data/sample-input.txt`

### Notes

- Exception/error handling could be better, but I didn't want to over-engineer that. So I left it with basic checks.
- Incorrect currency values and rates aren't validated.
- URLs for providers could be in config file.
- Exceptional cases not tested (ex. provider returns error)
