@if (Module::isEnabled('Language') && Route::has('admin.blogs.index'))
    <li
        class="nav-item dropdown {{ isRoute(['admin.blogs.*', 'admin.blog-category.*', 'admin.blog-comment.*'], 'active') }}">
        <a href="javascript:void()" class="nav-link has-dropdown"><i
                class="fas fa-newspaper"></i><span>{{ __('Manage Blogs') }}</span></a>

        <ul class="dropdown-menu">
            <li class="{{ isRoute('admin.blog-category.*', 'active') }}">
                <a class="nav-link" href="{{ route('admin.blog-category.index') }}">
                    {{ __('Category List') }}
                </a>
            </li>
            <li class="{{ isRoute('admin.blogs.*', 'active') }}">
                <a class="nav-link" href="{{ route('admin.blogs.index') }}">
                    {{ __('Post List') }}
                </a>
            </li>
            <li class="{{ isRoute('admin.blog-comment.*', 'active') }}">
                <a class="nav-link" href="{{ route('admin.blog-comment.index') }}">
                    {{ __('Post Comments') }}
                </a>
            </li>
        </ul>
    </li>
@endif
