from chromium_extension import ExtensionDir, ExtensionPackage

ext = ExtensionDir("./ext")
ext.writeToPackage("gears_test.crx")
pkg = ExtensionPackage("gears_test.crx")


