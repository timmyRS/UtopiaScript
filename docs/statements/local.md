# local

Alias: `.`

---

Declares a local variable, meaning that it is only accessible to the block it was defined in and sub-blocks:

    local var1 "Hello";
    {
        local var2 "World";
    };
    print var1; # Hello
    print var2; # produces an error
