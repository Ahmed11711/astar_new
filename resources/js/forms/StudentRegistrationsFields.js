export const fields = [
  { key: "email", label: "Email", required: 1, placeholder: "Enter Email", type: "text", isString: false },
  { key: "affiliation_type", label: "Affiliation Type", required: 1, placeholder: "Enter Affiliation Type", type: "select", isString: false,
      options: [
    {
        "value": "school",
        "label": "School"
    },
    {
        "value": "teacher",
        "label": "Teacher"
    }
] },
  { key: "user_id", label: "User Id", required: 1, placeholder: "Enter User Id", type: "number", isString: false },
  { key: "is_active", label: "Is Active", required: 1, placeholder: "Enter Is Active", type: "boolean", isString: false }
];