function confirmDelete(id) {
    var confirmDelete = confirm("Biztosan törölni szeretné ezt a költséget?");
    
    if (confirmDelete) {
        // Az input beállítása a törlendő elem azonosítójával
        document.getElementById("guaranteedDeleteId").value = id; // A törlendő elem azonosítója
        
        // A POST kérést küldjük el
        document.getElementById("deleteForm").submit();
    } else {
        // Ha a felhasználó "Mégse"-t választotta, semmi nem történik
    }
    
}
//JSt végülis nem tettem bele ezt is majd ha késöbb lesz rá igény hozzáadom