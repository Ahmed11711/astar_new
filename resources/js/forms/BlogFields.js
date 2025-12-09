export const fields = [
  { key: "title", label: "Title", required: 1, placeholder: "Enter Title", type: "text", isString: false },
  { key: "slug", label: "Slug", required: 1, placeholder: "Enter Slug", type: "text", isString: false },
  { key: "content", label: "Content", required: 1, placeholder: "Enter Content", type: "textarea", isString: false },
  { key: "img", label: "Img", required: 1, placeholder: "Enter Img", type: "image", isString: true },
  { key: "author_id", label: "Author Id", required: 1, placeholder: "Enter Author Id", type: "number", isString: false },
  { key: "is_published", label: "Is Published", required: 1, placeholder: "Enter Is Published", type: "boolean", isString: false }
];