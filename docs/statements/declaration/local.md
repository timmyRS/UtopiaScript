# local

- Alias: `.`
- Syntax: `local [type] <name> [[=|as] value]` 

Declares a local variable, meaning that it is only accessible to the block it was defined in and sub-blocks:

    local var1 "Hello";
    {
        local var2 "World";
    };
    print_line var1; # Hello
    print_line var2; # produces an error

[[ Run it online ]](https://utopia.sh/?code=local+var1+%22Hello%22%3B%0D%0A%7B%0D%0A++++local+var2+%22World%22%3B%0D%0A%7D%3B%0D%0Aprint_line+var1%3B+%23+Hello%0D%0Aprint_line+var2%3B+%23+produces+an+error)
