# Adding Third-party libraries via a Sub Profile

Libraries ultimately should go into `/web/libraries`. However a profile does not
have access to this directory. Therefore any libraries that need to go there
have to be copied from the profile. A profile should have its own `libraries`
directory that has its content copied into the root `/web/libraries`.

A script will need to be added to `composer.json` of the main distribution that
will copy this directory on install and updates.

```json
"scripts": {
  "post-install-cmd": [
    "cp -a web/profiles/subprofile-name/libraries/. web/libraries/"
  ],
  "post-update-cmd": [
    "cp -a web/profiles/subprofile-name/libraries/. web/libraries/"
  ]
}
```

## Overwriting libraries from the Sitefarm Seed base profile

The previous script will copy libraries from the sub profile into the main
`/web/libraries`. This means that by placing libraries in the sub profile's
`libraries` directory it will copy over the top of the libraries already added
by the base profile.
