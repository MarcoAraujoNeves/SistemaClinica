const { app, BrowserWindow, Menu, electron, ipcMain, ipcRenderer, shell } = require("electron");
const path = require('path');
const os = require('os');
const fs = require('fs');
// Menu.setApplicationMenu(false);
let win;
let splash;

function createMainWindow() {

    workerWindow = new BrowserWindow({
        show: false,
        webPreferences: {
            webSecurity: false,
            nodeIntegration: true,
            plugins: true
        }
    });
    workerWindow.loadURL("file://" + __dirname + "/dist/InterfaceServidor/assets/pdf/worker.html");
    //workerWindow.webContents.openDevTools();
    workerWindow.hide();

    win = new BrowserWindow({
        width: 900,
        height: 600,
        backgroundColor: "#ffffff",
        show: false,
        webPreferences: {
            nodeIntegration: true,
            webSecurity: false,
            enableRemoteModule: true
        }
        //icon: `file://${__dirname}/dist/InterfaceServidor/assets/img/logo.png`
    });
    //win.webContents.openDevTools();
    win.loadURL(`file://${__dirname}/dist/InterfaceServidor/index.html`);
 
    win.maximize();
    win.show();

    win.on("closed", function () {
        win = null;
    });


    workerWindow.on("closed", () => {
        workerWindow = undefined;
    });
}

function createSplashScreen() {
  
    splash = new BrowserWindow({
        width: 400,
        height: 475,
        frame: false,
        webPreferences: {
            nodeIntegration: true,
            enableRemoteModule: true

        }
    });

    splash.loadURL(`file://${__dirname}/dist/InterfaceServidor/assets/splash-screen/index.html`);
    //splash.webContents.openDevTools();
    splash.show();

    splash.on("close", createMainWindow);
    splash.on("closed", function () {
        splash = null;
    });
}

ipcMain.on('printPDF', (event, content) => {
    //console.log(content);
    workerWindow.webContents.send('printPDF', content);
});
// when worker window is ready
ipcMain.on("readyToPrintPDF",async (event) => {
    
    const date = new Date();
    const fileName = date.getDate()+"dia "+date.getMonth()+"mes "+date.getFullYear()+"ano "+date.getHours()+"h-"+date.getMinutes()+"m-"+date.getSeconds()+'s';
    
    const folderPath = path.join(os.homedir(),'Desktop','relatoriosAso');
 
     try {
      if (!fs.existsSync(folderPath)) {
        await fs.mkdir(folderPath, { recursive: true }, function(err) {
          if (err) {
            console.log(err)
          } else {
            console.log("New directory successfully created.")
          }
        })
      } 
    } catch(e) {
      console.log("An error occurred.")
    }
    
	await workerWindow.webContents.printToPDF({ silent:true, printSelectionOnly: false,printBackground: true,  landscape: false, pageSize: "A4" }).then((data) => {
       
        const pdfPath = process.platform !== "win32"?path.join(os.homedir(),`relatorio Aso ${fileName}.pdf`) :path.join(folderPath,`relatorio Aso ${fileName}.pdf`);
        
            fs.writeFile(pdfPath, data,function (error) {
            if (error) {
                throw error
            }
            event.sender.send('wrote-pdf', pdfPath)
            shell.openExternal(pdfPath);
        })
    }).catch((error) => {
        throw error;
    })
});

// app.on("ready", createMainWindow);
app.on("ready", createSplashScreen);

app.on("window-all-closed", function () {
    if (process.platform !== "darwin") {
        app.quit();
    }
});

app.on("activate", function () {
    if (splash === null) {
        // createMainWindow();
        createSplashScreen();
    }
});