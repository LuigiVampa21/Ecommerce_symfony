const links = document.querySelectorAll("[data-delete]");

console.log(links);

for(const link of links){
    link.addEventListener('click', async function(e) {
        e.preventDefault();
        
        if(confirm('Do you really want to remove this image ?')){
            try{

                const response = await fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                })
                console.log(response);
                this.parentElement.remove();
            }catch(err){
                alert(err);
                console.log(err);
            }

        }
    })
}