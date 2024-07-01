// indexeddb.js
let db;

const request = indexedDB.open('librasysDB', 1);

request.onerror = (event) => {
  console.log('Error opening IndexedDB:', event);
};

request.onsuccess = (event) => {
  db = event.target.result;
  console.log('IndexedDB opened successfully');
};

request.onupgradeneeded = (event) => {
  db = event.target.result;
  const objectStore = db.createObjectStore('librarians', { keyPath: 'email' });
  objectStore.createIndex('name', 'name', { unique: false });
  objectStore.createIndex('password', 'password', { unique: false });
  objectStore.createIndex('phone', 'phone', { unique: false });
  console.log('IndexedDB setup complete');
};

function addLibrarian(librarian) {
  const transaction = db.transaction(['librarians'], 'readwrite');
  const objectStore = transaction.objectStore('librarians');
  const request = objectStore.add(librarian);

  request.onsuccess = () => {
    console.log('Librarian added to IndexedDB');
  };

  request.onerror = (event) => {
    console.log('Error adding librarian:', event);
  };
}

function getLibrarians(callback) {
  const transaction = db.transaction(['librarians'], 'readonly');
  const objectStore = transaction.objectStore('librarians');
  const request = objectStore.getAll();

  request.onsuccess = () => {
    callback(request.result);
  };

  request.onerror = (event) => {
    console.log('Error fetching librarians:', event);
  };
}
