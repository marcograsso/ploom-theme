export default {
  plugins: [
    "preset-default",
    "removeDimensions",
    "sortAttrs",
    "cleanupListOfValues",
    {
      name: "removeAttrs",
      params: {
        attrs: ["class"],
      },
    },
    {
      name: "convertColors",
      params: {
        currentColor: true,
      },
    },
    {
      name: "addAttributesToSVGElement",
      params: {
        attributes: [
          {
            "aria-hidden": "true",
          },
        ],
      },
    },
  ],
};
