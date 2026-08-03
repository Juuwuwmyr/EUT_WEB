Set WshShell = CreateObject("WScript.Shell")
Dim fso: Set fso = CreateObject("Scripting.FileSystemObject")

strPath = fso.GetParentFolderName(WScript.ScriptFullName)
kitchenProfile = strPath & "\.chrome-kitchen"

localApp = WshShell.ExpandEnvironmentStrings("%LOCALAPPDATA%")
Dim chromePaths(2)
chromePaths(0) = "C:\Program Files\Google\Chrome\Application\chrome.exe"
chromePaths(1) = "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"
chromePaths(2) = localApp & "\Google\Chrome\Application\chrome.exe"

chromePath = ""
Dim i
For i = 0 To 2
    If fso.FileExists(chromePaths(i)) Then
        chromePath = chromePaths(i)
        Exit For
    End If
Next

kitchenUrl = "https://eut-delivery.duckdns.org/chef"

chromeFlags = " --app=" & kitchenUrl & _
              " --user-data-dir=""" & kitchenProfile & """" & _
              " --kiosk-printing" & _
              " --disable-popup-blocking" & _
              " --no-first-run" & _
              " --no-default-browser-check"

WshShell.Run "taskkill /f /im chrome.exe", 0, True
WScript.Sleep 1500

Dim prefsFile
prefsFile = kitchenProfile & "\Default\Preferences"
If fso.FileExists(prefsFile) Then
    fso.DeleteFile prefsFile, True
End If

If chromePath <> "" Then
    WshShell.Run """" & chromePath & """" & chromeFlags, 0, False
Else
    WshShell.Run "cmd /c start chrome" & chromeFlags, 0, False
End If
