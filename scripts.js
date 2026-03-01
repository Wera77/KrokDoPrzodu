var tabs = document.querySelectorAll('.tabs-age ul li');
var podst = document.querySelector('.podst');
var srednie = document.querySelector('.srednie');
var dorosli = document.querySelector('.dorosli');
var seniorzy = document.querySelector('.seniorzy');
var items = document.querySelectorAll('.lboard_item');

tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
        var currentdatali = tab.getAttribute("data-age");

        tabs.forEach(function(tab){
            tab.classList.remove('active');
        });

        tab.classList.add('active');

        items.forEach(function(item){
            item.style.display = "none";
        });

        if(currentdatali == "podst"){
            podst.style.display = "block";
        }
        else if(currentdatali == "srednie"){
            srednie.style.display = "block";
        }
        else if(currentdatali == "dorosli"){
            dorosli.style.display = "block";
        }
        else if(currentdatali == "seniorzy"){
            seniorzy.style.display = "block";
        }
    });
});

// --- PANEL 1: MIASTO ---
document.querySelectorAll(".tabs-city li").forEach(li => {
    li.addEventListener("click", () => {
        document.querySelector(".tabs-city").style.display = "none";
        document.querySelector(".tabs-type").style.display = "block";
    });
});

// --- PANEL 2: TYP ---
document.querySelectorAll(".tabs-type li").forEach(li => {
    li.addEventListener("click", () => {
        document.querySelector(".tabs-type li.active").classList.remove("active");
        li.classList.add("active");

        if (li.dataset.type === "ludzie") {
            document.querySelector(".tabs-type").style.display = "none";
            document.querySelector(".tabs-age").style.display = "block";
        } else {
            document.querySelector(".tabs-type").style.display = "none";
            document.querySelector(".tabs-age").style.display = "none";
        }
    });
});

// --- PANEL 3: WIEK ---
document.querySelectorAll(".tabs-age li").forEach(li => {
    li.addEventListener("click", () => {
        document.querySelector(".tabs-age li.active").classList.remove("active");
        li.classList.add("active");

        // tutaj możesz podpiąć leaderboard
        console.log("Wybrano grupę wiekową:", li.dataset.age);
    });
});