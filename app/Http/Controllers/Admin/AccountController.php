<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

abstract class AccountController extends Controller
{
    abstract protected function role(): string;

    abstract protected function label(): string;

    abstract protected function routeBase(): string;

    /** [column label, table, foreign key column] used for the count shown in the list */
    abstract protected function related(): array;

    public function index(Request $request)
    {
        [$relatedLabel, $table, $column] = $this->related();

        $accounts = User::query()
            ->select('users.*')
            ->addSelect(['related_count' => DB::table($table)
                ->selectRaw('count(*)')
                ->whereColumn("$table.$column", 'users.id')])
            ->where('role', $this->role())
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(fn ($w) => $w
                    ->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%'));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.accounts.index', $this->viewData(compact('accounts', 'relatedLabel')));
    }

    public function create()
    {
        return view('admin.accounts.create', $this->viewData(['account' => null]));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        User::create($data + ['role' => $this->role()]);

        return redirect()->route($this->routeBase() . '.index')
            ->with('success', $this->label() . ' berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('admin.accounts.edit', $this->viewData(['account' => $this->find($id)]));
    }

    public function update(Request $request, $id)
    {
        $account = $this->find($id);
        $data = $request->validate($this->rules($account->id));

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $account->update($data);

        return redirect()->route($this->routeBase() . '.index')
            ->with('success', $this->label() . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $account = $this->find($id);

        $blockers = [];

        if ($n = DB::table('orders')->where('user_id', $account->id)->count()) {
            $blockers[] = "$n pesanan";
        }
        if ($n = DB::table('products')->where('seller_id', $account->id)->count()) {
            $blockers[] = "$n produk";
        }
        if ($n = DB::table('negotiations')
            ->where(fn ($q) => $q->where('customer_id', $account->id)->orWhere('seller_id', $account->id))
            ->count()) {
            $blockers[] = "$n negosiasi";
        }

        if ($blockers) {
            return back()->with('error', 'Akun tidak bisa dihapus karena masih memiliki data: ' . implode(', ', $blockers) . '.');
        }

        try {
            $account->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Akun tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route($this->routeBase() . '.index')
            ->with('success', $this->label() . ' berhasil dihapus.');
    }

    private function find($id): User
    {
        // Scoped by role, so the customer pages can never touch a seller (and vice versa).
        return User::where('role', $this->role())->findOrFail($id);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($ignoreId)],
            'phone'    => ['nullable', 'string', 'max:25'],
            'address'  => ['nullable', 'string'],
            'password' => $ignoreId
                ? ['nullable', 'string', 'min:6']
                : ['required', 'string', 'min:6'],
        ];
    }

    private function viewData(array $extra = []): array
    {
        return $extra + [
            'label'     => $this->label(),
            'routeBase' => $this->routeBase(),
        ];
    }
}