export default [
    {
        key: "status",
        label: "Estado",
        model: null,
        options: [
            { id: null, label: "VER TODO" },
            { id: 1, label: "PENDIENTE" },
            { id: 2, label: "CANCELADO" },
            { id: 3, label: "ANULADO" },
        ],
        visible: true,
        clearable: true,
        cols: 12,
        md: 12,
    },
];

