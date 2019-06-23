# global

- Alias: `:`
- Syntax: `global <name> [[=] value]`

Declares a global variable.

Note that global variables, or variables in higher scopes in general, can be overwritten at any time:
    
    global greeting "Hi";
    local farewell "Bye";
    {
        local greeting "Hey";
        local farewell "Later";
        print greeting farewell; # HeyLater
    };
    print greeting farewell; # HiBye

[[ Run it online ]](https://utopia.sh/?code=global+greeting+%22Hi%22%3B%0D%0Alocal+farewell+%22Bye%22%3B%0D%0A%7B%0D%0A++++local+greeting+%22Hey%22%3B%0D%0A++++local+farewell+%22Later%22%3B%0D%0A++++print+greeting+farewell%3B+%23+HeyLater%0D%0A%7D%3B%0D%0Aprint+greeting+farewell%3B+%23+HiBye)

## See also

- [constant](constant): Declares an immutable global variable.
