<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Join Our App</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f9;">
    <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border: 1px solid #dddddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: #28a745; padding: 20px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 24px;">ShareFair</h1>
        </div>
        <!-- Body -->
        <div style="padding: 20px;">
            <h2 style="font-size: 20px; color: #333333;">Hi,</h2>
            <p style="font-size: 16px; color: #555555;">You have been added to the group "<strong>{{ $groupName }}</strong>" for expense sharing by <strong>{{ $name }}</strong>.</p>
            <p style="font-size: 16px; color: #555555;">Our platform is a powerful expense-sharing app that allows you to share expenses with your friends, family, or partners.</p>
            <h3 style="text-align: center; font-size: 18px; color: #333333;">Click the button below to join the app and start sharing expenses in "<strong>{{ $groupName }}</strong>."</h3>
            <!-- Button -->
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('requestJoin', ['email' => $useremail, 'name' => $username]) }}" 
                   style="background-color: #28a745; color: #ffffff; text-decoration: none; padding: 10px 20px; font-size: 16px; border-radius: 5px; display: inline-block; font-weight: bold;">
                   Join Request
                </a>
            </div>
            <p style="font-size: 14px; color: #999999; text-align: center;">If the button doesn’t work, click the link below:</p>
            <p style="font-size: 14px; word-break: break-word; text-align: center; color: #007bff;">
                <a href="{{ route('requestJoin', ['email' => $useremail, 'name' => $username]) }}" style="color: #007bff; text-decoration: none;">
                    {{ route('requestJoin', ['email' => $useremail, 'name' => $username]) }}
                </a>
            </p>
        </div>
        <!-- Footer -->
        <div style="background: #f4f4f9; padding: 10px; text-align: center; color: #888888; font-size: 12px;">
            <p style="margin: 0;">&copy; {{ date('Y') }} ShareFair. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
