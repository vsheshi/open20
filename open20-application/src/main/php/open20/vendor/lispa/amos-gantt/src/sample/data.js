var tasks = {
    data: [
        {
            "id": "p-1",
            "text": "Progettazione",
            "type": gantt.config.types.project
        }, {
            "id": "p-2",
            "text": "Sviluppo",
            "type": gantt.config.types.project
        },
        {
            "id": "p-3",
            "text": "Test",
            "type": gantt.config.types.project
        }, {
            "id": "p-4",
            "text": "Rilascio in produzione",
            "type": gantt.config.types.project
        },


        {"id": "p-5", "text": "Casi d'uso e processi", "type": gantt.config.types.project}, {
            "id": "t-1",
            "text": "Casi d'uso e processi",
            "start_date": "20-02-2017",
            "duration": 0.5,
            "progress": null,
            "open": null,
            "parent": "p-1",
            "type": gantt.config.types.task
        },


        {
            "id": "t-2",
            "text": "Selezione componente visualizzazione GANTT",
            "start_date": "28-02-2017",
            "duration": 0.6,
            "progress": null,
            "open": null,
            "parent": "p-1",
            "type": gantt.config.types.task
        },

        {
            "id": "t-3",
            "text": "Integrazione con il plugin Community",
            "start_date": "06-03-2017",
            "duration": 0.7,
            "progress": null,
            "open": null,
            "parent": "p-1",
            "type": gantt.config.types.task
        }, {
            "id": "t-5",
            "text": "CRUD per la gestione del progetto",
            "start_date": "01-03-2017",
            "duration": 0.8,
            "progress": null,
            "open": null,
            "parent": "p-1",
            "type": gantt.config.types.task
        }, {
            "id": "t-6",
            "text": "Scrittura documento AF",
            "start_date": "06-03-2017",
            "duration": 0.9,
            "progress": null,
            "open": null,
            "parent": "p-1",
            "type": gantt.config.types.task
        },


        {
            "id": "t-7",
            "text": "CRUD per la gestione del progetto (sviluppo)",
            "start_date": "15-03-2017",
            "duration": 1,
            "progress": null,
            "open": null,
            "parent": "p-2",
            "type": gantt.config.types.task
        }, {
            "id": "t-8",
            "text": "Visualizzazione GANTT",
            "start_date": "13-03-2017",
            "duration": 1.5,
            "progress": null,
            "open": null,
            "parent": "p-2",
            "type": gantt.config.types.task
        },


        {
            "id": "t-9",
            "text": "Integrazione CRUD con visualizzazione GANTT",
            "start_date": "30-03-2017",
            "duration": 1.2,
            "progress": null,
            "open": null,
            "parent": "p-2",
            "type": gantt.config.types.task
        }, {
            "id": "t-10",
            "text": "Testing",
            "start_date": "15-03-2017",
            "duration": 1.14,
            "progress": null,
            "open": null,
            "parent": "p-3",
            "type": gantt.config.types.task
        },


         {
         "id": "t-11",
         "text": "Aggiornamento ambiente di produzione",
         "start_date": "30-03-2017",
         "duration": 1.33,
         "progress": null,
         "open": null,
         "parent": "p-3",
         "type": gantt.config.types.task
         }

    ],

    links: [


        {"id": "t-2t-1", "source": "t-2", "target": "t-1", "type": "0"}, {
            "id": "t-3t-1",
            "source": "t-3",
            "target": "t-1",
            "type": "0"
        }, {"id": "t-5t-1", "source": "t-5", "target": "t-1", "type": "0"}, {
            "id": "t-6t-1",
            "source": "t-6",
            "target": "t-1",
            "type": "0"
        }, {"id": "t-6t-3", "source": "t-6", "target": "t-3", "type": "0"}, {
            "id": "t-6t-5",
            "source": "t-6",
            "target": "t-5",
            "type": "0"
        }, {"id": "t-7t-6", "source": "t-7", "target": "t-6", "type": "0"}, {
            "id": "t-8t-2",
            "source": "t-8",
            "target": "t-2",
            "type": "0"
        }, {"id": "t-8t-6", "source": "t-8", "target": "t-6", "type": "0"}, {
            "id": "t-9t-7",
            "source": "t-9",
            "target": "t-7",
            "type": "0"
        }, {"id": "t-9t-8", "source": "t-9", "target": "t-8", "type": "0"}, {
            "id": "t-11t-10",
            "source": "t-11",
            "target": "t-10",
            "type": "0"
        }

    ]

};