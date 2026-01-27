export const fields = [
  { key: "teacher_id", label: "Teacher Id", required: 1, placeholder: "Enter Teacher Id", type: "number", isString: false },
  { key: "title", label: "Title", required: 1, placeholder: "Enter Title", type: "text", isString: false },
  { key: "description", label: "Description", required: 1, placeholder: "Enter Description", type: "textarea", isString: false },
  { key: "type", label: "Type", required: 1, placeholder: "Enter Type", type: "select", isString: false,
      options: [
    {
        "value": "admin",
        "label": "Admin"
    },
    {
        "value": "school",
        "label": "School"
    }
] },
  { key: "response", label: "Response", required: 1, placeholder: "Enter Response", type: "textarea", isString: false },
  { key: "status", label: "Status", required: 1, placeholder: "Enter Status", type: "select", isString: false,
      options: [
    {
        "value": "pending",
        "label": "Pending"
    },
    {
        "value": "reviewed",
        "label": "Reviewed"
    },
    {
        "value": "resolved",
        "label": "Resolved"
    }
] }
];