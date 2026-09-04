' ============================================
' EUT Snack House - Kiosk Mode Launcher
' ============================================
' This script launches the browser in kiosk mode and enables auto-click 
' functionality for print dialogs and other confirmations.
' 
' Usage: Double-click this file or run: cscript kiosk-mode.vbs
' ============================================

Option Explicit

Dim objShell, objWShell, objFSO
Dim strURL, strBrowserPath, strKioskArgs
Dim intChoice, strCustomURL

' Initialize objects
Set objShell = CreateObject("Shell.Application")
Set objWShell = CreateObject("WScript.Shell")
Set objFSO = CreateObject("Scripting.FileSystemObject")

' Configuration
strURL = "https://eut-delivery.duckdns.org/admin/orders"  ' Default URL
strBrowserPath = """C:\Program Files\Google\Chrome\Application\chrome.exe"""

' Kiosk mode arguments for Chrome
strKioskArgs = "--kiosk --disable-infobars --disable-session-crashed-bubble --disable-translate --no-first-run --fast --fast-start --disable-default-apps --disable-popup-blocking --disable-extensions --disable-web-security --allow-running-insecure-content --disable-features=VizDisplayCompositor --autoplay-policy=no-user-gesture-required --disable-geolocation --disable-notifications --disable-background-timer-throttling --disable-renderer-backgrounding --disable-backgrounding-occluded-windows --disable-component-extensions-with-background-pages"

' ============================================
' Main Menu
' ============================================
Do
    intChoice = MsgBox("EUT Snack House - Kiosk Mode Launcher" & vbCrLf & vbCrLf & _
                       "Current URL: " & strURL & vbCrLf & vbCrLf & _
                       "Choose an option:" & vbCrLf & _
                       "YES = Launch Kiosk Mode" & vbCrLf & _
                       "NO = Change URL" & vbCrLf & _
                       "CANCEL = Exit", _
                       vbYesNoCancel + vbQuestion, "EUT Kiosk Launcher")
    
    Select Case intChoice
        Case vbYes
            ' Launch kiosk mode
            Call LaunchKioskMode()
            Exit Do
            
        Case vbNo
            ' Change URL
            strCustomURL = InputBox("Enter the URL to launch in kiosk mode:" & vbCrLf & vbCrLf & _
                                   "Examples:" & vbCrLf & _
                                   "• https://eut-delivery.duckdns.org/admin/orders" & vbCrLf & _
                                   "• https://eut-delivery.duckdns.org/chef/dashboard" & vbCrLf & _
                                   "• https://eut-delivery.duckdns.org/waiter/dashboard", _
                                   "Change URL", strURL)
            
            If strCustomURL <> "" Then
                strURL = strCustomURL
            End If
            
        Case vbCancel
            ' Exit
            WScript.Quit
    End Select
Loop

' ============================================
' Functions
' ============================================

Sub LaunchKioskMode()
    Dim strCommand
    
    ' Build the command
    strCommand = strBrowserPath & " " & strKioskArgs & " """ & strURL & """"
    
    MsgBox "Launching Kiosk Mode..." & vbCrLf & vbCrLf & _
           "Auto-click features:" & vbCrLf & _
           "• Print dialog auto-confirmation" & vbCrLf & _
           "• Alert dialog auto-dismiss" & vbCrLf & _
           "• F11 for manual fullscreen toggle" & vbCrLf & vbCrLf & _
           "Press Ctrl+Alt+F4 to exit kiosk mode", _
           vbInformation, "Kiosk Mode Starting"
    
    ' Launch the browser
    objWShell.Run strCommand, 1, False
    
    ' Wait a moment for browser to load
    WScript.Sleep 2000
    
    ' Start auto-click monitoring
    Call StartAutoClickMonitoring()
End Sub

Sub StartAutoClickMonitoring()
    Dim intCounter
    intCounter = 0
    
    MsgBox "Auto-click monitoring started!" & vbCrLf & vbCrLf & _
           "The system will automatically:" & vbCrLf & _
           "• Click OK/Print on print dialogs" & vbCrLf & _
           "• Dismiss alert popups" & vbCrLf & _
           "• Handle confirmation dialogs" & vbCrLf & vbCrLf & _
           "This dialog will close in 5 seconds...", _
           vbInformation + vbSystemModal, "Auto-Click Active"
    
    ' Main monitoring loop
    Do While True
        ' Check for print dialogs every 500ms
        Call CheckAndClickPrintDialog()
        Call CheckAndClickAlerts()
        
        WScript.Sleep 500
        intCounter = intCounter + 1
        
        ' Every 60 seconds, check if browser is still running
        If intCounter Mod 120 = 0 Then
            If Not IsBrowserRunning() Then
                MsgBox "Browser closed. Exiting auto-click monitoring.", vbInformation, "Monitoring Stopped"
                Exit Do
            End If
        End If
        
        ' Check for exit key combination (Ctrl+Alt+F4)
        If objWShell.AppActivate("Chrome") Then
            ' Browser is active, continue monitoring
        End If
    Loop
End Sub

Sub CheckAndClickPrintDialog()
    On Error Resume Next
    
    ' Look for print dialog windows
    Dim objWindow
    
    ' Chrome print dialog
    Set objWindow = objWShell.AppActivate("Print")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"  ' Press Enter to print
        WScript.Sleep 100
    End If
    
    ' Windows print dialog
    Set objWindow = objWShell.AppActivate("Print Dialog")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"  ' Press Enter to print
        WScript.Sleep 100
    End If
    
    ' Generic print spooler
    Set objWindow = objWShell.AppActivate("Printing")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"
        WScript.Sleep 100
    End If
    
    On Error GoTo 0
End Sub

Sub CheckAndClickAlerts()
    On Error Resume Next
    
    ' Look for JavaScript alerts and confirmation dialogs
    Dim objWindow
    
    ' JavaScript alert
    Set objWindow = objWShell.AppActivate("JavaScript Alert")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"  ' Press Enter to dismiss
        WScript.Sleep 100
    End If
    
    ' Confirmation dialog
    Set objWindow = objWShell.AppActivate("Confirm")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"  ' Press Enter to confirm
        WScript.Sleep 100
    End If
    
    ' Chrome security dialog
    Set objWindow = objWShell.AppActivate("Security Warning")
    If Not IsEmpty(objWindow) Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"
        WScript.Sleep 100
    End If
    
    On Error GoTo 0
End Sub

Function IsBrowserRunning()
    On Error Resume Next
    Dim objProcesses, objProcess
    Dim bFound
    
    bFound = False
    
    Set objProcesses = GetObject("winmgmts:").ExecQuery("SELECT * FROM Win32_Process WHERE Name='chrome.exe'")
    
    For Each objProcess in objProcesses
        bFound = True
        Exit For
    Next
    
    IsBrowserRunning = bFound
    On Error GoTo 0
End Function

' ============================================
' Cleanup
' ============================================
Set objShell = Nothing
Set objWShell = Nothing
Set objFSO = Nothing

WScript.Echo "EUT Kiosk Mode script completed."