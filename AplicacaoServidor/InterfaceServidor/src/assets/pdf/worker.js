const ipcRenderer = require("electron").ipcRenderer;

ipcRenderer.on("printPDF",async (event, content) => {
    document.body.innerHTML = content;
    await ipcRenderer.send("readyToPrintPDF");
});
