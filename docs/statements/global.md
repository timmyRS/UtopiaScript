# global

Alias: `:`

---

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

## See also

- [constant](constant): Declares an immutable global variable.
