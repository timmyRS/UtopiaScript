# constant

- Aliases: `const`, `!`
- Syntax: `constant <name> [[=] value]`

Declares an immutable [global](global) variable.

    constant farewell "Goodbye";
    {
        local farewell "Bye"; # produces an error
    };

[[ Run it online ]](https://utopia.sh/?code=constant+farewell+%22Goodbye%22%3B%0D%0A%7B%0D%0A++++local+farewell+%22Bye%22%3B+%23+produces+an+error%0D%0A%7D%3B)
