' ============================================
' Auto Print Dialog Clicker
' ============================================
' This script runs in the background and automatically clicks
' "Print" or "OK" buttons in print dialogs for seamless printing.
' 
' Usage: Run this script before starting the web application
' Command: cscript auto-print-clicker.vbs
' ============================================

Option Explicit

Dim objWShell, objFSO
Dim bRunning, intCounter

' Initialize objects
Set objWShell = CreateObject("WScript.Shell")
Set objFSO = CreateObject("Scripting.FileSystemObject")

bRunning = True
intCounter = 0

WScript.Echo "EUT Auto Print Clicker - Starting..."
WScript.Echo "This script will automatically handle print dialogs."
WScript.Echo "Press Ctrl+C to stop or close this window."
WScript.Echo ""

' Main monitoring loop
Do While bRunning
    ' Check for various print dialog types
    Call HandlePrintDialogs()
    Call HandleJavaScriptDialogs()
    Call HandleWindowsDialogs()
    
    ' Sleep for 300ms between checks
    WScript.Sleep 300
    intCounter = intCounter + 1
    
    ' Show status every 100 iterations (30 seconds)
    If intCounter Mod 100 = 0 Then
        WScript.Echo "[" & Now() & "] Monitoring active... (checked " & intCounter & " times)"
    End If
    
    ' Check if we should continue running
    If intCounter > 28800 Then  ' 8 hours max (28800 * 300ms)
        WScript.Echo "Auto-stop after 8 hours of monitoring."
        bRunning = False
    End If
Loop

WScript.Echo "Auto Print Clicker stopped."

' ============================================
' Functions
' ============================================

Sub HandlePrintDialogs()
    On Error Resume Next
    
    ' Chrome print preview dialog
    If objWShell.AppActivate("Print - Google Chrome") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-clicked Chrome print dialog"
        WScript.Sleep 200
    End If
    
    ' Generic print dialog
    If objWShell.AppActivate("Print") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-clicked generic print dialog"
        WScript.Sleep 200
    End If
    
    ' Windows print spooler
    If objWShell.AppActivate("Printing") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ESC}"  ' Close spooler dialog
        WScript.Echo "[" & Now() & "] Auto-dismissed print spooler"
        WScript.Sleep 200
    End If
    
    ' Print queue dialog
    If objWShell.AppActivate("Print Queue") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ESC}"
        WScript.Echo "[" & Now() & "] Auto-closed print queue"
        WScript.Sleep 200
    End If
    
    On Error GoTo 0
End Sub

Sub HandleJavaScriptDialogs()
    On Error Resume Next
    
    ' JavaScript alerts
    If objWShell.AppActivate("JavaScript Alert") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-dismissed JavaScript alert"
        WScript.Sleep 200
    End If
    
    ' Confirmation dialogs
    If objWShell.AppActivate("Confirm") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"  ' Click OK/Yes
        WScript.Echo "[" & Now() & "] Auto-confirmed dialog"
        WScript.Sleep 200
    End If
    
    ' Page unload confirmations
    If objWShell.AppActivate("Before you leave") Then
        WScript.Sleep 150
        objWShell.SendKeys "{TAB}{ENTER}"  ' Tab to "Leave" and press Enter
        WScript.Echo "[" & Now() & "] Auto-confirmed page leave"
        WScript.Sleep 200
    End If
    
    ' Location permission dialog - Chrome
    If objWShell.AppActivate("wants to") Then
        WScript.Sleep 200
        objWShell.SendKeys "{TAB}{TAB}{ENTER}"  ' Tab to "Allow this time" and press Enter
        WScript.Echo "[" & Now() & "] Auto-allowed location permission"
        WScript.Sleep 300
    End If
    
    ' Generic permission dialog
    If objWShell.AppActivate("Permission") Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"  ' Allow permission
        WScript.Echo "[" & Now() & "] Auto-handled permission dialog"
        WScript.Sleep 200
    End If
    
    On Error GoTo 0
End Sub

Sub HandleWindowsDialogs()
    On Error Resume Next
    
    ' Windows Security dialog
    If objWShell.AppActivate("Windows Security") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-handled Windows Security dialog"
        WScript.Sleep 200
    End If
    
    ' File download dialog
    If objWShell.AppActivate("File Download") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-handled file download"
        WScript.Sleep 200
    End If
    
    ' Certificate warning
    If objWShell.AppActivate("Certificate Warning") Then
        WScript.Sleep 150
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-accepted certificate"
        WScript.Sleep 200
    End If
    
    On Error GoTo 0
End Sub

' Cleanup
Set objWShell = Nothing
Set objFSO = Nothing