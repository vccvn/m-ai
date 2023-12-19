<?php

namespace App\Http\Controllers\Admin\CPanel;

use Illuminate\Http\Request;


trait PathMethods{
    
    public function nessage($message = '')
    {
        return $this->viewModule('message', compact('message'));
    }

    public function notFound()
    {
        return $this->nessage('File không tồn tại');
    }

    public function getHomePath(Request $request, $user = null)
    {
        return rtrim(base_path('/'),'/');
    }

    public function getPathData(Request $request)
    {
        $user = $request->user();
        $path = $request->p?$request->p:$request->path;
        $relativePath = ltrim($path, '/');
        $homePath = $this->getHomePath($request, $user);
        if(count(explode('../', $path)) >= 2 ) return [];
        $fullPath = $homePath . ($relativePath?'/' .rtrim($relativePath, '/') : '');

        return compact('user', 'path', 'relativePath', 'fullPath', 'homePath');
    }

}