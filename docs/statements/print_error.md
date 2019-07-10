# print_error

Aliases: `printerr`, `<!`, `≤`

Prints the given [string](../constructs#string) to the Utopia's error output and returns it, which can be seen via REPL:

    > print_error "You didn't do the thing!";
    < You didn't do the thing!
    = You didn't do the thing!

[[ Run it online ]](https://utopia.sh/repl#print_error%20%22You%20didn't%20do%20the%20thing!%22%3B)

## See also

- [print](print): Prints the given string to the Utopia's standard output and returns it.
- [print_error_line](print_error_line): Does the same as print, except that it appends `crlf` to the string.
