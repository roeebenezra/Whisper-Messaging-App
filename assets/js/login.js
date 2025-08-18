(function(){
  function setCookie(name,value){ document.cookie = name + "=" + value + "; Path=/; SameSite=Lax"; }
  function deleteCookie(name){ document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax"; }
  function send(action, body, onDone){
    var fd = new FormData();
    for (var k in body) { fd.append(k, body[k]); }
    fd.append("action", action);
    fetch("/login_api.php", { method: "POST", body: fd })
      .then(r => r.json()).then(onDone)
      .catch(err => { alert("Network error"); console.error(err); });
  }

  // If token already exists, set gate cookie and reload to load main bundle
  try {
    var t = localStorage.getItem("auth_token");
    if (t && document.cookie.indexOf("pre_auth_js=1") === -1) {
      setCookie("pre_auth_js","1");
      location.reload();
      return;
    }
  } catch(e){}

  // Wiring UI
  document.addEventListener("click", function(e){
    var el = e.target;
    if (!el) return;
    if (el.id === "btn_send_otp") {
      var username = document.getElementById("login_username").value.trim();
      var website  = document.getElementById("website") ? document.getElementById("website").value.trim() : "";
      if(!username){ alert("Enter username"); return; }
      send("request_otp", { username: username, website: website }, function(json){
        if(!json || !json.ok){ alert(json && json.error || "Failed to send OTP"); return; }
        document.getElementById("login_otp").style.display = "";
        document.getElementById("btn_verify_otp").style.display = "";
        document.getElementById("btn_send_otp").disabled = true;
        if(json.dev_code){ alert("DEV OTP: " + json.dev_code); console.log("DEV OTP:", json.dev_code); }
      });
    }
    if (el.id === "btn_verify_otp") {
      var username = document.getElementById("login_username").value.trim();
      var otp      = document.getElementById("login_otp").value.trim();
      var website  = document.getElementById("website") ? document.getElementById("website").value.trim() : "";
      if(!otp || otp.length < 6){ alert("Enter the 6-digit code"); return; }
      send("verify_otp", { username: username, otp: otp, website: website }, function(json){
        if(!json || !json.ok || !json.token){ alert(json && json.error || "Invalid code"); return; }
        try{
          localStorage.setItem("auth_token", json.token);
          localStorage.setItem("username", json.username || username);
        }catch(e){}
        setCookie("pre_auth_js","1");
        location.reload();
      });
    }
    if (el.id === "btn_logout") {
      try {
        localStorage.removeItem("auth_token");
        localStorage.removeItem("username");
      } catch(e){}
      deleteCookie("pre_auth_js");
      location.reload();
    }
  });
})();
