' ============================================
' Universal Dialog Killer
' ============================================
' This script aggressively closes ANY popup dialog in Chrome
' Useful for kiosk environments where no popups should appear
' 
' Usage: Run this as last resort when other scripts fail
' Command: cscript dialog-killer.vbs
' ============================================

Option Explicit

Dim objWShell
Set objWShell = CreateObject("WScript.Shell")

WScript.Echo "Universal Dialog Killer - Starting..."
WScript.Echo "This will aggressively close ALL popup dialogs."
WScript.Echo "Press Ctrl+C to stop."
WScript.Echo ""

Dim intCounter
intCounter = 0

' Main monitoring loop - faster than other scripts
Do While True
    ' Kill all common dialog types
    Call KillAllDialogs()
    
    ' Sleep for 100ms between checks (very fast response)
    WScript.Sleep 100
    intCounter = intCounter + 1
    
    ' Show status every 300 iterations (30 seconds)
    If intCounter Mod 300 = 0 Then
        WScript.Echo "[" & Now() & "] Dialog killer active... (checked " & intCounter & " times)"
    End If
    
    ' Auto-stop after 2 hours
    If intCounter > 72000 Then
        WScript.Echo "Auto-stop after 2 hours of monitoring."
        Exit Do
    End If
Loop

Sub KillAllDialogs()
    On Error Resume Next
    
    ' Chrome password dialogs
    If objWShell.AppActivate("Update password") Then
        objWShell.SendKeys "{ESC}"
        WScript.Sleep 50
        objWShell.SendKeys "{TAB}{ENTER}"  ' "No, thanks"
        WScript.Echo "[" & Now() & "] Killed password update dialog"
    End If
    
    If objWShell.AppActivate("Save password") Then
        objWShell.SendKeys "{ESC}"
        WScript.Sleep 50
        objWShell.SendKeys "{TAB}{ENTER}"
        WScript.Echo "[" & Now() & "] Killed save password dialog"
    End If
    
    ' Location permission
    If objWShell.AppActivate("wants to") Then
        objWShell.SendKeys "{TAB}{TAB}{ENTER}"  ' "Allow this time"
        WScript.Sleep 50
        objWShell.SendKeys "{ESC}"
        WScript.Echo "[" & Now() & "] Handled location permission"
    End If
    
    ' Notification dialogs
    If objWShell.AppActivate("notifications") Then
        objWShell.SendKeys "{ESC}"
        WScript.Sleep 50
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Killed notification dialog"
    End If
    
    ' Generic Chrome dialogs - try multiple approaches
    If objWShell.AppActivate("Chrome") Then
        ' Send ESC first (safest)
        objWShell.SendKeys "{ESC}"
        WScript.Sleep 20
        
        ' Then try Enter (for OK buttons)
        objWShell.SendKeys "{ENTER}"
        WScript.Sleep 20
        
        ' Then try Tab+Enter (for secondary buttons)
        objWShell.SendKeys "{TAB}{ENTER}"
        WScript.Sleep 20
    End If
    
    ' JavaScript alerts
    If objWShell.AppActivate("JavaScript") Then
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Killed JavaScript dialog"
    End If
    
    ' Confirmation dialogs
    If objWShell.AppActivate("Confirm") Then
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Killed confirmation dialog"
    End If
    
    ' Print dialogs
    If objWShell.AppActivate("Print") Then
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Auto-printed"
    End If
    
    ' Security warnings
    If objWShell.AppActivate("Security") Then
        objWShell.SendKeys "{ENTER}"
        WScript.Echo "[" & Now() & "] Bypassed security warning"
    End If
    
    On Error GoTo 0
End Sub

' Cleanup
Set objWShell = Nothing
WScript.Echo "Dialog killer stopped."