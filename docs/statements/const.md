# const

Aliases: `constant`, `!`

---

Declares an immutable [global](global) variable.

    const farewell "Goodbye";
    {
        local farewell "Bye"; # produces an error
    };
