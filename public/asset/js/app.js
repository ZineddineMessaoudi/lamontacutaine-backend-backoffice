const app = {
  baseUrl: window.location.origin,

  init: function () {
    console.log("App starting...")
    //When DOM content is loaded recovering data from API
    app.loadFromAPI()

    //adding event listener on event year selector
    const yearSelector = document.querySelector("#yearSelector")
    yearSelector.addEventListener("change", app.showYearSelected)
  },

  /**
   * Add events to DOM on event DOMContentLoaded
   */
  loadFromAPI: async function (yearValue) {
    //fetch with async/await
    try {
      if (yearValue > 0) {
        const response = await fetch(
          "https://127.0.0.1:8000/api/v1/events/" + yearValue
        )
        let responseArray = await response.json()
        // adding event from event array to DOM
        for (const event of responseArray.events) {
          app.addEventToDOM(event)
        }
      } else {
        const response = await fetch("https://127.0.0.1:8000/api/v1/events")
        let responseArray = await response.json()
        // adding event from event array to DOM
        for (const event of responseArray.events) {
          app.addEventToDOM(event)
        }
      }
    } catch (error) {
      console.error(
        "Erreur rencontrée lors de la récupération des évenements : " + error
      )
    }
  },

  addEventToDOM: function (event) {
    //Select event table
    const eventList = document.querySelector("#eventList")

    //Select template
    const template = document.querySelector("#eventTemplate")

    //Cloning template
    const clone = document.importNode(template.content, true)

    //adding event's properties to the table's row

    //title
    clone.querySelector("#title").textContent = event.title

    //startDate
    let startDate = new Date(event.start_date)

    clone.querySelector("#startDate").textContent =
      startDate.getDate() +
      "/" +
      (startDate.getMonth() + 1) +
      "/" +
      startDate.getFullYear()

    //endDate
    let endDate = new Date(event.end_date)

    clone.querySelector("#endDate").textContent =
      endDate.getDate() +
      "/" +
      (endDate.getMonth() + 1) +
      "/" +
      endDate.getFullYear()

    //inscriptionEndDate
    if (event.inscription_end_date) {
      let inscriptionEndDate = new Date(event.inscription_end_date)
      let formatedInscriptionEndDate =
        inscriptionEndDate.getDate() +
        "/" +
        (inscriptionEndDate.getMonth() + 1) +
        "/" +
        inscriptionEndDate.getFullYear()
      clone.querySelector("#inscriptionEndDate").textContent =
        formatedInscriptionEndDate
    } else {
      clone.querySelector("#inscriptionEndDate").textContent =
        "Pas d'inscription requise"
    }

    //MaximumCapacity
    clone.querySelector("#maximumCapacity").textContent = event.maximum_capacity

    //openToTrader
    if (event.open_to_trader) {
      clone
        .querySelector("#isOpenToTrader")
        .setAttribute(
          "class",
          "badge rounded-pill text-bg-success bi bi-check-circle"
        )
    } else {
      clone
        .querySelector("#isOpenToTrader")
        .setAttribute(
          "class",
          "badge rounded-pill text-bg-danger bi bi-x-circle"
        )
    }

    //mediasLink
    clone
      .querySelector("#mediaLink")
      .setAttribute("href", app.baseUrl + "/media/evenement/" + event.id)

    //editLink
    clone
      .querySelector("#eventEdit")
      .setAttribute(
        "href",
        app.baseUrl + "/evenements/" + event.id + "/mise-a-jour"
      )

    //deleteId
    const deleteButton = clone.querySelector("#deleteId")
    deleteButton.setAttribute("data", event.id)

    //adding event listener on delete button to change the target of delete action
    deleteButton.addEventListener("click", app.addEventIdToDeleteModal)

    //Adding clone to the events table
    eventList.appendChild(clone)
  },

  // Handle the modal target on delete event
  addEventIdToDeleteModal: function (e) {
    const modalAction = document.querySelector("#eventDeleteAction")
    modalAction.setAttribute(
      "action",
      app.baseUrl +
        "/evenements/" +
        e.target.getAttribute("data") +
        "/supprimer"
    )
  },

  showYearSelected: function () {
    let yearValue = document.querySelector("#yearSelector").value
    document.querySelector("#eventList").textContent = ""
    app.loadFromAPI(yearValue)
  },
}

document.addEventListener("DOMContentLoaded", app.init)
