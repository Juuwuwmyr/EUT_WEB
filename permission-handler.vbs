' ============================================
' Chrome Permission Dialog Handler
' ============================================
' This script specifically handles Chrome browser permission popups
' including location, notifications, camera, microphone permissions
' 
' Usage: Run this before or alongside your web application
' Command: cscript permission-handler.vbs
' ============================================

Option Explicit

Dim objWShell
Set objWShell = CreateObject("WScript.Shell")

WScript.Echo "Chrome Permission Handler - Starting..."
WScript.Echo "This will automatically handle browser permission dialogs."
WScript.Echo "Press Ctrl+C to stop."
WScript.Echo ""

Dim intCounter
intCounter = 0

' Main monitoring loop
Do While True
    ' Check for Chrome permission dialogs
    Call HandleChromePermissions()
    
    ' Sleep for 200ms between checks (faster than other scripts)
    WScript.Sleep 200
    intCounter = intCounter + 1
    
    ' Show status every 150 iterations (30 seconds)
    If intCounter Mod 150 = 0 Then
        WScript.Echo "[" & Now() & "] Permission monitoring active... (checked " & intCounter & " times)"
    End If
    
    ' Auto-stop after 4 hours
    If intCounter > 72000 Then
        WScript.Echo "Auto-stop after 4 hours of monitoring."
        Exit Do
    End If
Loop

Sub HandleChromePermissions()
    On Error Resume Next
    
    ' Chrome location permission dialog (exact text match)
    If objWShell.AppActivate("eut-delivery.duckdns.org wants to") Then
        WScript.Sleep 300
        ' Try multiple key combinations to click "Allow this time"
        objWShell.SendKeys "{TAB}{TAB}{ENTER}"
        WScript.Sleep 100
        objWShell.SendKeys "{TAB}{ENTER}"
        WScript.Sleep 100
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-clicked 'Allow this time' for location"
        WScript.Sleep 500
    End If
    
    ' Generic Chrome wants to dialog
    If objWShell.AppActivate("wants to") Then
        WScript.Sleep 300
        objWShell.SendKeys "{TAB}{TAB}{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-handled Chrome wants to dialog"
        WScript.Sleep 300
    End If
    
    ' Chrome notification permission
    If objWShell.AppActivate("Show notifications") Then
        WScript.Sleep 200
        objWShell.SendKeys "{TAB}{ENTER}"  ' Allow notifications
        WScript.Echo "[" & Now() & "] Auto-allowed notifications"
        WScript.Sleep 200
    End If
    
    ' Chrome microphone permission
    If objWShell.AppActivate("Use your microphone") Then
        WScript.Sleep 200
        objWShell.SendKeys "{TAB}{ENTER}"  ' Allow microphone
        WScript.Echo "[" & Now() & "] Auto-allowed microphone"
        WScript.Sleep 200
    End If
    
    ' Chrome camera permission
    If objWShell.AppActivate("Use your camera") Then
        WScript.Sleep 200
        objWShell.SendKeys "{TAB}{ENTER}"  ' Allow camera
        WScript.Echo "[" & Now() & "] Auto-allowed camera"
        WScript.Sleep 200
    End If
    
    ' Generic permission request
    If objWShell.AppActivate("Permission request") Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-handled permission request"
        WScript.Sleep 200
    End If
    
    ' Chrome security warning
    If objWShell.AppActivate("Not secure") Then
        WScript.Sleep 200
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-handled security warning"
        WScript.Sleep 200
    End If
    
    ' Try to focus back on Chrome if any dialog was handled
    objWShell.AppActivate("Chrome")
    
    On Error GoTo 0
End Sub

' Cleanup
Set objWShell = Nothing
WScript.Echo "Permission handler stopped."