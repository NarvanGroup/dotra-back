$baseUrl = "http://localhost:8000/api"

function Get-RandomMobile {
    return "09" + (Get-Random -Minimum 100000000 -Maximum 999999999).ToString()
}

function Test-Endpoint {
    param (
        [string]$Uri,
        [string]$Method,
        [hashtable]$Body,
        [string]$Description,
        [bool]$ExpectSuccess
    )

    Write-Host "Testing: $Description" -NoNewline
    
    try {
        $params = @{
            Uri = $Uri
            Method = $Method
            ContentType = "application/json"
            ErrorAction = "Stop"
        }
        if ($Body) {
            $params.Body = ($Body | ConvertTo-Json)
        }

        $response = Invoke-RestMethod @params
        
        if ($ExpectSuccess) {
            Write-Host " [PASS]" -ForegroundColor Green
            # Write-Host ($response | ConvertTo-Json -Depth 2)
        } else {
            Write-Host " [FAIL] Expected failure but got success" -ForegroundColor Red
        }
    } catch {
        if (-not $ExpectSuccess) {
            Write-Host " [PASS] (Expected Failure)" -ForegroundColor Green
            # Write-Host $_.Exception.Message
        } else {
            Write-Host " [FAIL] $($_.Exception.Message)" -ForegroundColor Red
            if ($_.Exception.Response) {
                $stream = $_.Exception.Response.GetResponseStream()
                $reader = New-Object System.IO.StreamReader($stream)
                $content = $reader.ReadToEnd()
                $content | Out-File "error_log.html"
                Write-Host "Error details saved to error_log.html"
            }
        }
    }
    Write-Host ""
}

# 1. Customer Signup
$customerMobile = Get-RandomMobile
Test-Endpoint -Uri "$baseUrl/customer/signup" -Method "Post" -Body @{ mobile = $customerMobile; nId = "1234567890" } -Description "Customer Signup ($customerMobile)" -ExpectSuccess $true

# 2. Customer Send OTP (Existing)
Test-Endpoint -Uri "$baseUrl/customer/sendOtp" -Method "Post" -Body @{ mobile = $customerMobile } -Description "Customer Send OTP (Existing)" -ExpectSuccess $true

# 3. Customer Send OTP (Non-existent)
$randomMobile = Get-RandomMobile
Test-Endpoint -Uri "$baseUrl/customer/sendOtp" -Method "Post" -Body @{ mobile = $randomMobile } -Description "Customer Send OTP (Non-existent)" -ExpectSuccess $false

# 4. Vendor Signup
$vendorMobile = Get-RandomMobile
Test-Endpoint -Uri "$baseUrl/vendor/signup" -Method "Post" -Body @{ mobile = $vendorMobile; nId = "1234567890" } -Description "Vendor Signup ($vendorMobile)" -ExpectSuccess $true

# 5. Vendor Send OTP (Existing)
Test-Endpoint -Uri "$baseUrl/vendor/sendOtp" -Method "Post" -Body @{ mobile = $vendorMobile } -Description "Vendor Send OTP (Existing)" -ExpectSuccess $true

# 6. Vendor Send OTP (Non-existent)
$randomMobile2 = Get-RandomMobile
Test-Endpoint -Uri "$baseUrl/vendor/sendOtp" -Method "Post" -Body @{ mobile = $randomMobile2 } -Description "Vendor Send OTP (Non-existent)" -ExpectSuccess $false
