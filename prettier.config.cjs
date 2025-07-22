module.exports = {
  singleQuote: false,
  twigSingleQuote: false,
  twigOutputEndblockName: true,
  twigAlwaysBreakObjects: false,
  twigMultiTags: ["tag,endtag", "switch,case,default,endswitch"],
  plugins: [
    "@prettier/plugin-php",
    "@zackad/prettier-plugin-twig",
    "prettier-plugin-tailwindcss",
  ],
  overrides: [
    {
      files: ["*.php"],
      options: {
        parser: "php",
      },
    },
  ],
};
