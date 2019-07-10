# print

Aliases: `output`, `write`, `echo`, `say`, `<`

Prints the given [string](../constructs#string) to the Utopia's standard output and returns it, which can be seen via REPL:

    > print "Hello, world!";
    < Hello, world!
    = Hello, world!

[[ Run it online ]](https://utopia.sh/repl#print%20%22Hello%2C%20world!%22%3B)

## See also

- [print_line](print_line): Does the same as print, except that it appends `crlf` to the string.
- [print_error](print_error): Prints the given string to the Utopia's error output and returns it.
