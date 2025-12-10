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
            xmlns: "http://www.w3.org/2000/svg",
            "aria-hidden": "true",
          },
        ],
      },
    },
  ],
};
