function toggle() {
  let ul = document.querySelector("#ul-help");
  ul.classList.toggle("block");
}

if(document.getElementById('register')){
  this.addEventListener('submit', function(event) {
    let password = document.getElementById('pass').value;
    let conf_password = document.getElementById('conf-pass').value;
    if (password.length < 8) {
        event.preventDefault();
        document.querySelector('.error').innerText = "Password should be at least 8 characters long.";
    } else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
        event.preventDefault();
        document.querySelector('.error').innerText = "Password should contain at least one uppercase letter, one lowercase letter, and one digit.";
    }
    if(password!==conf_password){
      event.preventDefault();
      document.querySelector('.error').innerText = "Password doesn't match Confirm Password.";
    }
  })
};

function conf(){
    let conf =confirm("Are You Sure?");
    if(conf === true){
      return true;
    }else{
      return false;
    }
}

if(document.getElementById('ch-pass')){
  document.getElementById('ch-pass').addEventListener('submit', function(event) {
    let new_password = document.getElementById('new-pass').value;
    let conf_password = document.getElementById('conf-pass').value;
    if (new_password.length < 8) {
        event.preventDefault();
        document.querySelector('#p-error').innerText = "Password should be at least 8 characters long.";
    } else if (!/[a-z]/.test(new_password) || !/[A-Z]/.test(new_password) || !/[0-9]/.test(new_password)) {
        event.preventDefault();
        document.querySelector('#p-error').innerText = "Password should contain at least one uppercase letter, one lowercase letter, and one digit.";
    }
    if(new_password!==conf_password){
      event.preventDefault();
      document.querySelector('#p-error').innerText = "Password doesn't match Confirm Password.";
    }
  })
};

function search() {
  let searchBar = document.querySelector(".search-input").value.toUpperCase();
  let productList = document.querySelector(".products");
  let product = productList.querySelectorAll(".card");

  for (let i = 0; i < product.length; i++) {
    let match = product[i].getElementsByTagName("h4")[0];
    if (match) {
      let textValue = match.textContent || match.innerHTML;
      if (textValue.toUpperCase().indexOf(searchBar) > -1) {
        product[i].style.display = "";
      } else {
        product[i].style.display = "none";
      }
    }
  }
}

function showpass() {
  let pass=document.getElementById("pass");
  let icon=document.getElementById("icon");
    if(pass.type=="password"){
      pass.type = "text";
      icon.style.color="#0c64f1";
    }else{
      pass.type = "password";
      icon.style.color="";
    }
  }

function showconf(){
  let conf=document.getElementById("conf-pass");
  let icon=document.getElementById("icon-conf");
    if(conf.type=="password"){
      conf.type = "text";
      icon.style.color="#0c64f1";
    }else{
      conf.type = "password";
      icon.style.color="";
    }
  }

function showcur(){
  let cur=document.getElementById("cur-pass");
  let icon=document.getElementById("icon");
    if(cur.type=="password"){
      cur.type = "text";
      icon.style.color="#0c64f1";
    }else{
      cur.type = "password";
      icon.style.color="";
    }
  }

function shownew(){
  let newpass=document.getElementById("new-pass");
  let icon=document.getElementById("icon-new");
    if(newpass.type=="password"){
      newpass.type = "text";
      icon.style.color="#0c64f1";
    }else{
      newpass.type = "password";
      icon.style.color="";
    }
  }

function submit(){
  document.getElementById("form").submit();
}

function showform() {
  let ul = document.querySelector(".ch-pass");
  ul.classList.toggle("block");
}

function hideform() {
  let ul = document.querySelector(".ch-pass");
  ul.classList.remove("block");
}

if(document.getElementById("dark-mood")){
  document.getElementById("dark-mood").addEventListener("change",function (){
    let expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear()+1);
    let expire = expirationDate.toUTCString();
    if(this.checked){
      document.cookie= "dark-mood=Y; expires=" + expire +"; path=/";
      location.reload();
    }else{
      document.cookie = "dark-mood=; expires=wed, 02 Nov 2023 00:00:00 UTC; path=/";
      location.reload();
    }
  })
}

if(document.getElementById("hd-status")){
  document.getElementById("hd-status").addEventListener("change",function (){
    let expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear()+1);
    let expire = expirationDate.toUTCString();
    if(this.checked){
      document.cookie= "hd-status=Y; expires=" + expire +"; path=/";
    }else{
      document.cookie= "hd-status=; expires=wed, 02 Nov 2023 00:00:00 UTC; path=/";
    }
  })
}

if(document.getElementById("hd-cart")){
  document.getElementById("hd-cart").addEventListener("change",function (){
    let expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear()+1);
    let expire = expirationDate.toUTCString();
    if(this.checked){
      document.cookie= "hd-cart=Y; expires=" + expire +"; path=/";
    }else{
      document.cookie= "hd-cart=; expires=wed, 02 Nov 2023 00:00:00 UTC; path=/";
    }
  })
}

if(document.getElementById("hd-product")){
  document.getElementById("hd-product").addEventListener("change",function (){
    let expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear()+1);
    let expire = expirationDate.toUTCString();
    if(this.checked){
      document.cookie= "hd-product=Y; expires=" + expire +"; path=/";
    }else{
      document.cookie= "hd-product=; expires=wed, 02 Nov 2023 00:00:00 UTC; path=/";
    }
  })
}

if(document.querySelector(".write")){
  let text = document.querySelector(".write").innerHTML;
  let text2 = "You can Buy Or Sell Anything From Our Store";
  let index = 1;
  let index2 = 1;
  function writing(){
    document.querySelector(".write").innerHTML= text.slice(0,index);
    index++;
    if(index > text.length){
      index = 1;
    }
    setTimeout(function(){
      document.querySelector("h2").innerHTML= text2.slice(0,index2);
      index2++;
      if(index2 > text2.length){
        index2 = 1;
      }
    },5000)
  }
  setInterval(function(){
    writing();
  },250);
}
