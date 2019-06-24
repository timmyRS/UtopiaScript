# Declaration Statements

- [local](local)
- [final](final)
- [global](global)
- [constant](constant)
- [set](set)
- [unset](unset)

The first four are "initial declaration statements," as they can be used to create variables, unlike the rest, which only work with existing variables.
Furthermore, as the creators of variables, they can be used to force variables to only have values of a given [type](../../types.md):

    local string greeting "Hello";
    set greeting = true; # produces an error

[[ Run it online ]](https://utopia.sh/?code=local+string+greeting+%22Hello%22%3B%0D%0Aset+greeting+%3D+true%3B+%23+produces+an+error)
