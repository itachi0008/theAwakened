function memberLogin() {
    $('.js-login-button').on("click", function() {
        var data = {
            'email': $('#signin-email').val(),
            'password': $('#signin-password').val()
        };

        $.ajax({
            type: "POST",
            url: "/api/member/login",
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(data, statusMessage, xhr) {
                if (200 == xhr.status) {
                    $('.signin-success').html("Logged in successfully")
                }
            },
            error: function(data) {
                if (401 == data.status) {
                    $('.wrong-credentials').html("Wrong Credentials");
                }
            },
            complete: function(data, statusMessage) {
                if (200 == data.status) {
                    $('.signin-success').html("Logged in successfully")
                }
            }
        });
    })
}

function memberRegistration() {
    $('.js-signup-button').on("click", function() {
        var data = {
            'name': $('#signup-name').val(),
            'username': $('#signup-name').val(),
            'email': $('#signup-email').val(),
            'password': $('#signup-password').val()
        }
        $.ajax({
            type: "POST",
            url: "/api/member",
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(data, statusMessage, xhr) {
                if (201 == xhr.status) {
                    $('.signup-success').html("Registration complete")
                }
            },
            error: function(data) {
                if (409 == data.status) {
                    $('.user-exists').html("User exists")
                }
            },
            complete: function(data) {
                if (201 == data.status) {
                    $('.signup-success').html("Registration complete")
                }
            },
        });
    })
}

module.exports = {
    memberLogin: memberLogin,
    memberRegistration: memberRegistration
};
