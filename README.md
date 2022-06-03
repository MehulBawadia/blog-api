## Laravel Blog API with multiple roles.

### Installation steps

Run the following commands step-by-step

```
git clone git@github.com:MehulBawadia/blog-api.git
cd blog-api
composer install
cp .env.example .env
php artisan key:generate
// Update DB credentials in the .env file
php artisan migrate --seed
php artisan serve
```

Seeding the database will create 3 roles namely admin-user, manager-user, and regular-user.
Along with roles, it will also create 3 users assigning 1 role to 1 user.

I have used [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum) to authenticate the users via the token. The token gets generated every time a user is logged in. You will have to copy that token and paste it at subsequent request. Failing to do so will result in error.

I have used [Spatie's Laravel Permission package](https://spatie.be/docs/laravel-permission/v5/basic-usage/basic-usage) to map the roles to the user.

---

### API list

I have used Insomnia to test the APIs. You are free to use PostMan, or Insomnia. It's completely upto you. Following APIs have been created, and tested.

```
-- For admin performing CRUD operations on users

POST   /login
GET    /admin/users
POST   /admin/users
GET    /admin/users/:id
PUT    /admin/users/:id
DEL    /admin/users/:id
POST   /logout
```

```
-- For admin, manager, and regular-user performing CRUD operations on Posts

POST   /login
GET    /posts
POST   /posts
GET    /posts/:slug
PUT    /posts/:id
DEL    /posts/:id
POST   /logout
```

----

### Notes
- `admin-user` and `manager-user` can perform CRUD operations on all the posts.
- `regular-user` can perform CRUD operations only on their posts.
- Only `admin-user` can perform CRUD operations on users
