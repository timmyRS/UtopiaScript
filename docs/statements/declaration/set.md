# set

- Syntax: `set <name> [=|as] <new_value>`

Overwrites a variable, producing an error if it wasn't yet defined.

    local greeting;
    {
        set greeting "Hi";
    };
    print_line greeting; # Hi
    set farewell "Bye"; # produces an error

[[ Run it online ]](https://utopia.sh/?code=local+greeting%3B%0D%0A%7B%0D%0A++++set+greeting+%22Hi%22%3B%0D%0A%7D%3B%0D%0Aprint_line+greeting%3B+%23+Hi%0D%0Aset+farewell+%22Bye%22%3B+%23+produces+an+error)

However, if you don't want/need to assert that the variable you're overwriting exists, you can simply define a variable under the same name to the same effect:

    local greeting "Hello";
    local greeting "Hi";
    print greeting; # Hi

[[ Run it online ]](https://utopia.sh/?code=local+greeting+%22Hello%22%3B%0D%0Alocal+greeting+%22Hi%22%3B%0D%0Aprint+greeting%3B+%23+Hi)
