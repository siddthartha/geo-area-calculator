var components = {
    "packages": [
        {
            "name": "qunit",
            "main": "qunit-built.js"
        }
    ],
    "shim": {
        "qunit": {
            "exports": "QUnit"
        }
    },
    "baseUrl": "components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}