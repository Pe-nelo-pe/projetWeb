window.addEventListener("load", function(){

    const modalBet = document.querySelectorAll(".fast-bet");
    const btnBet = document.querySelectorAll(".bet");

    modalBet.forEach(modal => {
        btnBet.forEach(btn => {
            btn.addEventListener('click', () => {
            if (typeof modal.showModal === "function") {
                modal.showModal();
            }
        });
        });
        
   })


   const catalogue = document.querySelector("#catalogue")
   const btnGrid = document.querySelector(".btnGrid")
   const btnList = document.querySelector(".btnList")

   if(btnGrid){
     btnGrid.addEventListener("click", () =>{
           	catalogue.classList.replace("list", "grid")
            btnGrid.classList.add("actived")
            btnList.classList.remove("actived")
        });
   }

       

   if(btnList){
     btnList.addEventListener("click", () =>{
           	catalogue.classList.replace("grid", "list")
            btnList.classList.add("actived")
            btnGrid.classList.remove("actived")
        });
   }
       

   const zoom = document.querySelector(".zoom-img")
   const imgZoom = document.querySelector("#img-zoom")

   if(zoom){
    zoom.addEventListener("click", ()=>{
        imgZoom.classList.toggle("hidden")
    })
    
   }


})