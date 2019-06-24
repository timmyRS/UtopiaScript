# unset

- Aliases: `dispose`, `dispose_of`

Unsets a variable as long as it exists and is not immutable.

    local greeting "Hello";
    print_line greeting; # Hello
    unset greeting;
    print_line greeting; # produces an error

[[ Run it online ]](https://utopia.sh/?code=local+greeting+%22Hello%22%3B%0D%0Aprint_line+greeting%3B+%23+Hello%0D%0Aunset+greeting%3B%0D%0Aprint_line+greeting%3B+%23+produces+an+error)
