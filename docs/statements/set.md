# set

Alias: `$`

---

Overwrites a variable, producing an error if it wasn't yet defined.

    local greeting;
    {
        set greeting "Hi";
    };
    print greeting; # Hi
    set farewell "Bye"; # produces an error
