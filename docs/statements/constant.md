# constant

Aliases: `const`, `!`

---

Declares an immutable [global](global) variable.

    const farewell "Goodbye";
    {
        local farewell "Bye"; # produces an error
    };

[[ Run it online ]](https://utopia.sh/?code=const+farewell+%22Goodbye%22%3B%0D%0A%7B%0D%0A++++local+farewell+%22Bye%22%3B+%23+produces+an+error%0D%0A%7D%3B)
