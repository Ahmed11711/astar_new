export const fields = [
  { key: "key", label: "Key", required: 1, placeholder: "Enter Key", type: "text", isString: false },
  { key: "label", label: "Label", required: 1, placeholder: "Enter Label", type: "text", isString: false },
  { key: "type", label: "Type", required: 1, placeholder: "Enter Type", type: "select", isString: false,
      options: [
    {
        "value": "boolean",
        "label": "Boolean"
    },
    {
        "value": "number",
        "label": "Number"
    },
    {
        "value": "text",
        "label": "Text"
    }
] }
];