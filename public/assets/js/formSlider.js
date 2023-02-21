function slider(formTarget) {

    let connexionForm = document.querySelector('.formConnexion')
    let inscriptionForm = document.querySelector('.formInscription')

    switch(formTarget) {
        case 'inscription':
            connexionForm.setAttribute('style', 'transform: translateX(-110%);')
            inscriptionForm.setAttribute('style', 'transform: translateX(-110%);')
            break

        case 'connexion':
            connexionForm.setAttribute('style', 'transform: translateX(0);')
            inscriptionForm.setAttribute('style', 'transform: translateX(0);')
            break
    }
}